<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('chart_of_account_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('check_number')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_branch')->nullable();
            $table->text('narration')->nullable();
            $table->float('net_total', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('chart_of_account_id');
            $table->dropColumn('account_number');
            $table->dropColumn('check_number');
            $table->dropColumn('bank');
            $table->dropColumn('bank_branch');
            $table->dropColumn('narration');
            $table->dropColumn('net_total');
        });
    }
}
