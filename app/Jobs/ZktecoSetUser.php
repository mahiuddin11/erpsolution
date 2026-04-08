<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rats\Zkteco\Lib\ZKTeco;

class ZktecoSetUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    /**
     * 
     */
    public $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $zkteco = new ZKTeco('192.168.0.109');
        $zktecoConn = $zkteco->connect();
        if ($this->employee && $zktecoConn) {
            $uid = $this->employee->id;
            $userId = $this->employee->id;
            $name = $this->employee->name;
            $password = "";
            $role = 0;
            $zkteco->setUser($uid, $userId, $name, $password, $role);
        }
    }
}
