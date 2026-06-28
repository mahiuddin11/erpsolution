<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixOpeningBalancePrecisionInChartOfAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            DB::statement('ALTER TABLE chart_of_accounts MODIFY COLUMN opening_balance DECIMAL(30,2) DEFAULT 0'); //fix 99999999.99 issue 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            DB::statement('ALTER TABLE chart_of_accounts MODIFY COLUMN opening_balance FLOAT(10,2) DEFAULT 0');
        });
    }
}
