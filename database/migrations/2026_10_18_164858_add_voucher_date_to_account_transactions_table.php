<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherDateToAccountTransactionsTable extends Migration
{
  
    public function up()
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->date('voucher_date')->nullable()->after('type');
        });
    }

    public function down()
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropColumn('voucher_date');
        });
    }
}
