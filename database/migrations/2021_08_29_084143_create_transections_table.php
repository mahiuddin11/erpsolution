<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transections', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->foreignId('employee_id')->nullable();
            // $table->integer('to_account')->nullable();
            $table->integer('branch_id')->nullable();
            // $table->integer('expense_id')->nullable();
            $table->integer('payment_id')->nullable();
            $table->integer('type')->nullable()->comment('opening_balance=1, balance_transfer=2, Transfer Receive=3, expense=4,customer opening balance=5, supplier payment=6, balance transrfer = 7, customer payment = 8, supplier payment =9, cash sale =10, cash purchase=11, project money=12, service=13 , Return=14 , Employee Salary => 15 , Lone => 16');
            $table->date('date')->nullable();
            $table->float('debit', 10, 2)->nullable();
            $table->float('credit', 10, 2)->nullable();
            $table->float('amount', 10, 2)->nullable();
            $table->longtext('note')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
            //  $table->index(['branch_id','account_id','from_account','to_account','expense_id']);
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
        Schema::dropIfExists('transections');
    }
}
