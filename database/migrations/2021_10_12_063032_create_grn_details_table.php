<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grn_details', function (Blueprint $table) {
            $table->id();
            $table->integer('good_rcv_note_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('qty')->nullable();
            $table->integer('approve_qty')->default(0)->nullable();
            $table->integer('purchase_voucher')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('total_price')->nullable();
            // $table->enum('status', ['Accepted', 'Pending', 'Cancel'])->default('Pending');
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
        Schema::dropIfExists('grn_details');
    }
}
