<?php

namespace App\Console\Commands;

use App\Jobs\ZktecoSetUser;
use App\Models\Employee;
use Illuminate\Console\Command;
use Rats\Zkteco\Lib\ZKTeco;

class AutoSetUserZkteco extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'User:Store';

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
            $zktecoUsers = $zkteco->getUser();
            $collectData = collect($zktecoUsers);
            $zktecoUserId = $collectData->pluck('userid')->toArray();
            $employees = Employee::WhereNotIn('id', $zktecoUserId)->get();
        }

        if (count($employees) != 0) {
            foreach ($employees as $key => $employee) {
                dispatch(new ZktecoSetUser($employee));
            }
        }   
    }
}
