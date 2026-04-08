<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->index();
            $table->foreignId('table_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('account_id');
            $table->enum('type', [
                'purchase',
                'sale',
                'project',
                'supplier',
                'debit_voucher',
                'credit_voucher',
                'contra_voucher',
                'journal_voucher',
                'deposit_customer',
                'return_deposit',
                'project_expense',
                'opening_balance',
                'asset',
                'opening_stock'
            ])->comment('Transaction types');
            $table->decimal('debit', 10, 2)->nullable();
            $table->decimal('credit', 10, 2)->nullable();
            $table->longText('remark')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('employee_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->foreignId('created_by');
            $table->timestamps();

            // Adding indices for faster searches
            $table->index('branch_id');
            $table->index('account_id');
            $table->index('supplier_id');
            $table->index('customer_id');
            $table->index('employee_id');
            $table->index('project_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_transactions');
    }
}
