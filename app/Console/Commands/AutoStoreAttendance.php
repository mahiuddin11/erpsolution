<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Console\Command;
use Rats\Zkteco\Lib\ZKTeco;

class AutoStoreAttendance extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $zkteco = new ZKTeco('192.168.0.109');
        $conn = $zkteco->connect();
        if ($conn) {
            $zktecoAtten = $zkteco->getAttendance();
            $attendances = collect($zktecoAtten);
            $todayDate = date('Y-m-d');
            $attendances = $attendances->filter(function ($item) use ($todayDate) {
                return substr($item['timestamp'], 0, 10) == $todayDate;
            });

            foreach ($attendances as $key => $atten) {
                $branch_id = Employee::findOrFail($atten['id']);
                $createDate = date_create($atten['timestamp']);
                $date = date_format($createDate, 'Y-m-d');

                $employee = Attendance::Where('emplyee_id', $atten['id'])->whereDate('date', $date)->first();
                if ($employee) {
                    $attend = $attendances->filter(function ($item) use ($date, $atten) {
                        return substr($item['timestamp'], 0, 10) == $date && $item['id'] == $atten['id'];
                    });

                    $endTime = $attend->SortByDesc('timestamp')->first();
                    $endTime = date_create($endTime['timestamp']);
                    $endTime = date_format($endTime, 'H:i');

                    $employee->update([
                        'date'          => $date,
                        'sign_out'      => $endTime,
                    ]);
                } else {
                    $attend = $attendances->filter(function ($item) use ($date, $atten) {
                        return substr($item['timestamp'], 0, 10) == $date && $item['id'] == $atten['id'];
                    });
                    $entryTime = $attend->SortBy('timestamp')->first();
                    $entryTime = date_create($entryTime['timestamp']);
                    $entryTime = date_format($entryTime, 'H:i');

                    Attendance::create([
                        'emplyee_id'    => $atten['id'],
                        'branch_id'     => $branch_id->branch_id,
                        'date'          => $date,
                        'sign_in'       => $entryTime,
                        'sign_out'      => '00.00',
                    ]);
                }
            }
            return 1;
        }
    }
}
