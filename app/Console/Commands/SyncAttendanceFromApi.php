<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncAttendanceFromApi extends Command
{
    protected $signature = 'attendance:sync';
    protected $description = 'Sync attendance records from remote API every minute';


    public function handle()
    {
        try {
            $token = zktecoGetToken(); 

            if (!$token) {
                Log::error('Attendance Sync Failed: Token not retrieved.');
                return;
            }

            $startDate = now()->subDays(2)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');

            $url = env('ZKTECO_IP') . "/iclock/api/transactions/?start_time={$startDate}%2000:00:00&end_time={$endDate}%2023:59:59&page_size=1000";

            // $url = env('ZKTECO_IP') . '/iclock/api/transactions/?page_size=500';
            // $url = env('ZKTECO_IP') . "/iclock/api/transactions/?start_time={$today}%2000:00:00&end_time={$today}%2023:59:59&page_size=1000";

            // dd('hello iam here', $token, $url );
            // $response = Http::withHeaders([
            //     'Authorization' => "Token $token",
            // ])->get($url);

            $response = Http::withHeaders([
                'Authorization' => "Token $token",
                'Content-Type'  => 'application/json',
                ])->get($url);

        
            if ($response->successful()) {
                $data = $response->json();

                // dd($data);
                if (!empty($data['data'])) {
                    $groupedPunches = [];

                    // Group punches by employee and date
                    foreach ($data['data'] as $punch) {
                        if (empty($punch['emp_code']) || empty($punch['punch_time'])) {
                            continue;
                        }

                        $employee = Employee::where("id_card", $punch['emp_code'])->first();
                        $employeeId = $employee->id ?? 0;

                        // ==================== CROSS MIDNIGHT  ====================
                        $date =  getEffectiveDate($punch['punch_time']);
                        
                        $time = date('H:i:s', strtotime($punch['punch_time']));
                        $groupedPunches[$employeeId][$date][] = $time;

                        // Log::info("Employee Code:" . $employeeId);
                    }
                    // Now process each employee's punches
                    foreach ($groupedPunches as $employeeId => $dates) {
                        foreach ($dates as $date => $times) {
                            $this->saveAttendance($employeeId, $date, $times);
                        }
                    }
                }
                
            } else {
                Log::error('Attendance API fetch failed: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Attendance sync error: ' . $e->getMessage());
        }
    }

    private function saveAttendance($employeeId, $date, $times)
    {

        sort($times); // Sort times ascending

        $signIn = $times[0]; // first punch
        $signOut = count($times) > 1 ? end($times) : null; // last punch if multiple

        $attendance = Attendance::where('emplyee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->emplyee_id = $employeeId;
            $attendance->branch_id = Branch::first()->id;
            $attendance->date = $date;
            $attendance->sign_in = $signIn;

            if($signIn){
                $attendance->latitude = config("officeLocation.latitude");  
                $attendance->longitude = config("officeLocation.longitude") ; 
            }
        }
        
        $attendance->sign_out = $signOut;
        if($signOut){
            $attendance->latitude_out =  config("officeLocation.latitude");;
            $attendance->longitude_out = config("officeLocation.longitude");
        }

        $attendance->save();
    }
}
