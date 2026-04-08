<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_vouchers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('form_id')->unsigned();
            $table->string('voucher_no')->nullable();
            $table->integer('branch_id')->unsigned();
            $table->integer('store_id')->unsigned();
            $table->float('debit',20,2)->nullable();
            $table->float('credit',20,2)->nullable();
            $table->integer('status_id')->unsigned();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['form_id','branch_id','store_id']);

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
        Schema::dropIfExists('all_vouchers');
    }
}
