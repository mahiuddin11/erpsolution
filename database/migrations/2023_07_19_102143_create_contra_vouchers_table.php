<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContraVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contra_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('account_id')->nullable();
            $table->string('date')->nullable();
            $table->longText('note')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
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
        Schema::dropIfExists('contra_vouchers');
    }
}
