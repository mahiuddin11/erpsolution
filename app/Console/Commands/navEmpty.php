<?php

namespace App\Console\Commands;

use App\Models\Navigation;
use App\Models\RoleAccess;
use App\Models\UserRole;
use Illuminate\Console\Command;

class navEmpty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'navEmpty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Navigation db empty and seed done';

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
        
        Navigation::query()->truncate();
        UserRole::query()->truncate();
        RoleAccess::query()->truncate();
        \Illuminate\Support\Facades\Artisan::call('db:seed NavigationSeeder');
        return "Navigation db empty and seed done";
    }
}
