<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_id')->unsigned();
            $table->integer('from_branch_id')->unsigned();
            $table->integer('to_branch_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('qty')->unsigned();
            $table->integer('approve_qty')->nullable();
            $table->date('date')->nullable();
            $table->float('unit_price', 12, 2)->nullable();
            $table->float('total_price', 12, 2)->nullable();
            $table->enum('status', ['Approved', 'Inactive', 'Pending', 'Cancel'])->default('Pending')->comment('default status set active , penidng status waiting for approbal');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['transfer_id', 'to_branch_id', 'product_id']);
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
        Schema::dropIfExists('transfer_details');
    }
}
