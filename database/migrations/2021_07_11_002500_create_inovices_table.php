<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInovicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inovices', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('customer_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('store_id')->unsigned();
            $table->integer('invoice_no')->nullable();
            $table->string('payment_type')->nullable();
            $table->float('subtotal',12,2)->nullable();
            $table->float('discount',12,2)->nullable();
            $table->float('grand_total',12,2)->nullable();
            $table->integer('loder')->nullable();
            $table->integer('transportation')->nullable();
            $table->float('paid_amount',12,2)->nullable();
            $table->float('dur_amount',12,2)->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            // $table->tinyInteger('status')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['branch_id','store_id']);

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
        Schema::dropIfExists('inovices');
    }
}
