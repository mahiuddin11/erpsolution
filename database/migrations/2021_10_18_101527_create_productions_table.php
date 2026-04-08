<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('productionCode', 15)->unique();
            $table->integer('branch_id')->unsigned();
            $table->string('product_type', 10)->nullable();
            $table->integer('category_id')->unsigned();
            $table->string('name', 120)->nullable();
            $table->integer('brand_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->float('purchases_price', 20, 2)->nullable();
            $table->float('sale_price', 20, 2)->nullable();
            $table->integer('conversion_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['branch_id', 'product_id']);
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
        Schema::dropIfExists('productions');
    }
}
