<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckNumberToDabitVoucherDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dabit_voucher_details', function (Blueprint $table) {
            $table->string("check_number")->nullable();
            $table->string("check_date")->nullable();
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
