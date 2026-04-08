<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOpeningStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_opening_stocks', function (Blueprint $table) {
            $table->id();
            $table->string("invoice_no")->nullable();
            $table->foreignId("branch_id")->nullable();
            $table->foreignId("project_id")->nullable();
            $table->foreignId("created_by")->nullable();
            $table->string("date")->nullable();
            $table->string("qty")->nullable();
            $table->string("total_price")->nullable();
            $table->longText("narration")->nullable();
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
        Schema::dropIfExists('product_opening_stocks');
    }
}
