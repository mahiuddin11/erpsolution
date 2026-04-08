<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->string('invoice_no')->nullable();
            $table->integer('supplier_id')->unsigned();
            $table->integer('purchase_requisition_id')->unsigned();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->float('advance_payment', 10, 2)->nullable();
            $table->float('total_bill', 10, 2)->nullable();
            $table->enum('status', ['Complete','Accepted', 'Pending', 'Cancel'])->default('Pending');
            $table->string('note')->nullable();
            $table->integer('approved_by')->nullable();
            $table->date('approved_at')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
}
