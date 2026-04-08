<?php

namespace App\Console;

use App\Models\Employee;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    //   protected $commands = [
    //         Commands\navEmpty::class,
    //         \App\Console\Commands\SyncAttendanceFromApi::class,
    //     ];

    protected $commands = [
        Commands\navEmpty::class,
        \App\Console\Commands\SyncAttendanceFromApi::class,
        \App\Console\Commands\AutoStoreAttendance::class, 
        \App\Console\Commands\AutoCloseAttendance::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {


        // $schedule->command('attendance:sync')
        //     ->everyFiveMinutes()
        //     ->withoutOverlapping() 
        //     ->runInBackground();

        // $schedule->command('attendance:sync')
        //  ->everyFiveMinutes()
        //  ->withoutOverlapping() // আগের command শেষ না হলে নতুনটি run হবে না
        //  ->runInBackground()    // background এ run করবে
        //  ->appendOutputTo(storage_path('logs/attendance_sync.log'));

        $schedule->command('attendance:sync')->everyMinute();

        $schedule->command('attendance:attendanceAutoclose')
            ->dailyAt('22:00');

        // $schedule->call(function () {
        //     try {
        //         $tables = [];
        //         $takeEmployee = Employee::get();
        //         foreach ($takeEmployee as $employee) {
        //             $tables[] = [v
        //                 "employee_id" =>  $employee->id,
        //                 "name" => $employee->name,
        //                 "date" => now(),
        //                 "total_salary" => $employee->salary,
        //                 "basic_salary" => EMPLOYEE_BASIC_SALARY($employee->salary),
        //                 "house_rent" =>  EMPLOYEE_HOUSE_RENT_SALARY($employee->salary),
        //                 "medical_allowance" =>  600,
        //                 "travel_allowance" =>  350,
        //                 "food_allowance" =>  900,
        //                 "working_day" =>  MONTH_WORKING_DAY(),
        //                 "employee_presence_day" =>  EMPLOYEE_PRESENCE_DAY($employee->id),
        //                 "employee_absence_day" =>  EMPLOYEE_ABSENCE_DAY($employee->id),
        //                 "employee_late" => LATE_DAYS($employee),
        //                 "employee_paid_leave" => PAID_LEAVE_COUNT($employee),
        //                 "employee_unpaid_leave" => UNPAID_LEAVE_COUNT($employee),
        //                 "overtime_houre" => OVERTIME_HOURE($employee),
        //                 "overtime_salary" => OVERTIME_SALARY($employee),
        //                 "employee_payable_salary" =>  EMPLOYEE_PAYABLE_SALARY($employee),
        //                 "created_at" => now(),
        //                 "updated_at" => now()
        //             ];
        //         }

        //         DB::table('monthly_payable_salaries')->insert($tables);
        //     } catch (\Throwable $th) {
        //         return dd($th->getMessage());
        //     }
        // // })->everyMinute();
        // })->lastDayOfMonth('23:30');
        // $schedule->command('User:Store')->everyFourMinutes();
        // $schedule->command('attendance:store')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
