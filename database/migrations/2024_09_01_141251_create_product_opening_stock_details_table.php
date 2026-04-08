<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOpeningStockDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_opening_stock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_opening_stock_id");
            $table->integer('branch_id')->unsigned()->index();
            $table->integer('project_id')->unsigned()->index();
            $table->integer('category_id')->unsigned()->index();
            $table->string('purchasetype')->nullable();
            $table->integer('product_id')->unsigned()->index();
            $table->date('date')->nullable();
            $table->float('unit_price', 12, 2)->nullable();
            $table->float('total_price', 12, 2)->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('product_opening_stock_details');
    }
}
