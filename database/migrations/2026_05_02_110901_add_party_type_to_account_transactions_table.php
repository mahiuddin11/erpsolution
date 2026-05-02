<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartyTypeToAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            //
            $table->string('party_type')->nullable()->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            //
            $table->dropColumn('party_type');
        });
    }
}
