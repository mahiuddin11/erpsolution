<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('balance_transfer_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('from_account_id')->nullable();
            $table->integer('to_account_id')->nullable();
            $table->integer('branch_id')->unsigned();
            $table->float('debit', 10, 2)->nullable();
            $table->float('credit', 10, 2)->nullable();
            $table->float('amount', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->string('note')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('balance_transfer_logs');
    }
}
