<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAjdustmentDetailstsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_ajdustment_detailsts', function (Blueprint $table) {
            $table->id();
            $table->integer('purchases_id')->unsigned()->index();
            $table->integer('branch_id')->unsigned()->index();
            $table->integer('category_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->date('date')->nullable();
            $table->date('approval_date')->nullable();
            $table->float('unit_price', 12, 2)->nullable();
            $table->float('total_price', 12, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->enum('status', ['Active', 'Pending', 'Cancel'])->default('Pending')->comment('default status set Pending , Active status waiting for approbal');
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
        Schema::dropIfExists('stock_ajdustment_detailsts');
    }
}
