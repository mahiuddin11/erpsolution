<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostCenterToDabitVoucherDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dabit_voucher_details', function (Blueprint $table) {
            // $table->foreignId("branch_id")->nullable();
            $table->foreignId("project_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dabit_voucher_details', function (Blueprint $table) {
            //
        });
    }
}
