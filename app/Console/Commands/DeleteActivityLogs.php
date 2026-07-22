<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activitylogs:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete activity logs older than 30 days';

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
        $deleted = DB::table('activity_logs')->where('created_at', '<', Carbon::now()->subDays(30))->delete();
        $this->info("Deleted {$deleted} old activity logs.");
        return Command::SUCCESS;
    }
}
