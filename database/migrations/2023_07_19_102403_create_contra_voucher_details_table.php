<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContraVoucherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contra_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contra_voucher_id');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('account_id');
            $table->foreignId('to_account_id');
            $table->string('code')->nullable();
            $table->float('amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contra_voucher_details');
    }
}
