<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAjdustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_ajdustments', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('branch_id')->unsigned();
            $table->string('invoice_no')->nullable();
            $table->float('subtotal', 12, 2)->nullable();
            $table->float('grand_total', 12, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->enum('status', ['Active', 'Pending', 'Cancel'])->default('Pending')->comment('default status set Penidng , Active status waiting for approbal');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('approval_qty')->nullable();
            $table->integer('approve_by')->nullable();
            $table->date('approval_date')->nullable();
            $table->index(['branch_id']);
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
        Schema::dropIfExists('stock_ajdustments');
    }
}
