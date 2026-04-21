<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBonusNameToEmpPayDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emp_pay_details', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('bonus_id')->nullable();
            $table->string('total_bonus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emp_pay_details', function (Blueprint $table) {
            //
            $table->dropColumn([
                'bonus_id',
                'total_bonus'
            ]);
        });
    }
}
