<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjusts', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->date('check_date')->nullable();
            $table->integer('check_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->date('date')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('day')->nullable()->comment('expire date convert to day');
            $table->float('debit', 10, 2)->nullable();
            $table->float('credit', 10, 2)->nullable();
            $table->longtext('note')->nullable();
            $table->enum('payment_type', ['Credit', 'Deposit'])->default('Credit');
            $table->integer('user_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('adjusts');
    }
}
