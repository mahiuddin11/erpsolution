<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 10)->unique();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->date('date')->nullable();
            $table->integer('approve_by')->nullable();
            $table->integer('total_qty')->nullable();
            $table->integer('total_price')->nullable();
            $table->date('approve_at')->nullable();
            $table->integer('update_by')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', ['Complete','Accepted', 'Pending', 'Cancel'])->default('Pending');
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('purchase_requisitions');
    }
}
