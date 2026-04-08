<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('invoice_no')->nullable();
            $table->integer('supplier_id')->unsigned();
            $table->integer('purchase_voucher_id')->unsigned();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('total_price')->nullable();
            $table->integer('total_qty')->nullable();
            $table->enum('status', ['approve', 'Pending'])->default('Pending');
            // $table->float('advance_payment', 10, 2)->nullable();
            // $table->float('payment', 10, 2)->nullable();
            // $table->float('due', 10, 2)->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('grns');
    }
}
