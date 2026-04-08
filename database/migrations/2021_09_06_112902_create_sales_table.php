<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('date')->nullable();
            $table->integer('qty')->nullable();
            $table->enum('payment_type', ['Cash', 'Credit', 'Deposit', 'Due'])->default('Cash');
            $table->enum('sale_type', ['Regular', 'Production'])->default('Regular');
            $table->float('net_total', 10, 2)->nullable();
            $table->float('discount', 10, 2)->nullable();
            $table->float('sub_total', 10, 2)->nullable();
            $table->float('partialPayment', 10, 2)->nullable();
            $table->float('grand_total', 10, 2)->nullable();
            $table->longtext('narration')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
