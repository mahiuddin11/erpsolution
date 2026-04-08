<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToDabitVoucherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dabit_vouchers', function (Blueprint $table) {
            $table->integer('approve')->default(0)->comment('0 by not approve 1 by approved');
            $table->integer('approved_by')->nullable();
            $table->integer('viewed')->default(0)->comment('0 by not view 1 by viewd');
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
