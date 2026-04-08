<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalAmountToCashReqs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_reqs', function (Blueprint $table) {
            $table->double("approval_amount")->nullable();
            $table->string("bank_name")->nullable();
            $table->string("check_number")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_reqs', function (Blueprint $table) {
            $table->dropColumn("approval_amount");
        });
    }
}
