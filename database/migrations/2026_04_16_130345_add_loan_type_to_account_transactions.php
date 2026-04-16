<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLoanTypeToAccountTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE account_transactions MODIFY COLUMN `type` ENUM(
        'purchase','sale','project','supplier','debit_voucher','credit_voucher',
        'contra_voucher','journal_voucher','deposit_customer','return_deposit',
        'project_expense','opening_balance','asset','opening_stock',
        'salary_payment',
        'employee_loan',
        'loan_recovery',
        'salary_advance',
        'employee_deduction',
        'balance_transfer',
        'transfer_receive',
        'supplier_payment',
        'customer_payment',
        'cash_sale',
        'cash_purchase',
        'project_money',
        'service_income',
        'sales_return',
        'general_expense'
    ) NOT NULL");
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE account_transactions MODIFY COLUMN type ENUM(
        'purchase','sale','project','supplier','debit_voucher','credit_voucher',
        'contra_voucher','journal_voucher','deposit_customer','return_deposit',
        'project_expense','opening_balance','asset','opening_stock'
    )");
    }
}
