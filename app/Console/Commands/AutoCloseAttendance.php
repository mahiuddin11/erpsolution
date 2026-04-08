<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCloseAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:attendanceAutoclose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic Attendance check out';

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
        $today = Carbon::today()->format('Y-m-d');

        
        // যাদের শুধু sign_in আছে, sign_out নেই এবং auto_checkout = true
        
        $openAttendances = Attendance::where('date' , $today)->whereNotNull('sign_in')->whereNull('sign_out')
        ->whereHas('employe', function($q){
            $q->where('auto_checkout', true);
        })->get();

        foreach ($openAttendances as $attendance ) {
     
            try {
                $shiftEndTime = '18:15:00';

                $data = [
                    'sign_out' => $shiftEndTime,
                    'latitude_out' => config("officeLocation.latitude"),
                    'longitude_out' => config("officeLocation.longitude"),
                ];
                $attendance->update($data);
               
            } catch (\Exception $e) {
                Log::error("Failed to auto close attendance for Employee ID: {$attendance->employee_id}", [
                    'error' => $e->getMessage()
                ]);
            }

        }
            
    }
}
