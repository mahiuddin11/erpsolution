<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('sale_detail_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('returned_qty', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('vat_percent', 5, 2)->default(0);
            $table->decimal('line_amount', 15, 2);
            $table->enum('condition', ['good', 'damaged']);
            $table->string('reason')->nullable();

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
        Schema::dropIfExists('sale_return_details');
    }
}
