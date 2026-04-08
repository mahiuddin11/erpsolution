<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryChalansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_chalans', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('chalan_no', 10)->unique();
            $table->integer('sale_id')->unsigned();
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('customer_id')->unsigned()->nullable();
            $table->longText('note')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('delivery_chalans');
    }
}
