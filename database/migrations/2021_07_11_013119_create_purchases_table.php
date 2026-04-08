<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('supplier_id')->unsigned();
            $table->enum('type', ['Branch', 'Project']);
            $table->integer('project_id')->default(0);
            $table->integer('branch_id')->default(0);
            $table->string('invoice_no')->nullable();
            $table->string('custom_invoice');
            $table->string('payment_type')->nullable();
            $table->float('subtotal', 12, 2)->nullable();
            $table->float('discount', 12, 2)->nullable();
            $table->float('grand_total', 12, 2)->nullable();
            $table->integer('loder')->nullable();
            $table->integer('transportation')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('paid_amount', 12, 2)->nullable();
            $table->float('due_amount', 12, 2)->nullable();
            $table->enum('status', ['Active', 'Reopen', 'Pending', 'Cancel', 'Close'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['supplier_id', 'branch_id']);
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
        Schema::dropIfExists('purchases');
    }
}
