<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_deposits', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->date('date')->nullable();
            $table->float('amount', 10, 2)->nullable();
            $table->enum('payment_type', ['Return'])->default('Return');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('return_deposits');
    }
}
