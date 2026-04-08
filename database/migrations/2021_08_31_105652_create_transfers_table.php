<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code', 10)->uniqid();
            $table->integer('from_branch_id')->unsigned();
            $table->integer('to_branch_id')->unsigned();
            $table->date('date')->nullable();
            $table->integer('approve_qty')->nullable();
            $table->date('approved_date')->nullable();
            $table->float('net_total', 12, 2)->nullable();
            $table->float('subtotal', 12, 2)->nullable();
            $table->float('shipping', 12, 2)->nullable();
            $table->longText('note')->nullable();
            $table->enum('status', ['Approved', 'Inactive', 'Pending', 'Cancel'])->default('Pending')->comment('default status set active , penidng status waiting for approbal');
            $table->integer('qty')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['from_branch_id', 'to_branch_id']);
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
        Schema::dropIfExists('transfers');
    }
}
