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

            // Fixed: was referencing 'sales_returns' (plural) but the actual table
            // created above is 'sale_returns' (singular). Column also renamed
            // sale_return_id (was sales_return_id) to match the table name it points to.
            $table->foreignId('sale_return_id')->constrained('sale_returns')->onDelete('cascade');

            $table->foreignId('sale_detail_id')->constrained('sales__details');
            $table->foreignId('product_id')->constrained('products');

            $table->decimal('original_qty', 15, 2);
            $table->decimal('already_returned_qty', 15, 2)->default(0);
            $table->decimal('returned_qty', 15, 2);

            $table->decimal('unit_price', 15, 2);
            $table->decimal('line_tax', 15, 2)->default(0);
            $table->decimal('line_discount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2);

            $table->enum('condition', ['good', 'damaged'])->default('good');
            $table->enum('stock_action', ['restock', 'scrap'])->default('restock');

            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['sale_return_id']);
            $table->index(['sale_detail_id']);
            $table->index(['product_id']);
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
