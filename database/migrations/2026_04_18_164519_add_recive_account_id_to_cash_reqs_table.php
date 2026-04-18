<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReciveAccountIdToCashReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_reqs', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('account_id')->nullable()->after('approval_amount');
            $table->unsignedBigInteger('recive_account_id')->nullable()->after('account_id');
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
            $table->dropColumn(['account_id', 'recive_account_id']);
        });
    }
}
