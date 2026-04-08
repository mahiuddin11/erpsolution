<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('account_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('employee_id')->nullable();
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
        Schema::dropIfExists('credit_vouchers');
    }
}
