<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->string('invoice_no')->nullable();
            $table->integer('purchase_requisition_id')->unsigned();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->enum('status', ['Accepted', 'Pending', 'Cancel'])->default('Pending');
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
        Schema::dropIfExists('project_transfers');
    }
}
