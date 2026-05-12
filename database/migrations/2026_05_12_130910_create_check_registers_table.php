<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_registers', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_ref')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->date('cleared_date')->nullable();
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->unsignedBigInteger('to_account_id')->nullable();
            $table->string('payee_type')->nullable();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->enum('status', ['Issued', 'Printed', 'In_Transit', 'Cleared', 'Void', 'Stopped'])->default('Issued');
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('check_registers');
    }
}
