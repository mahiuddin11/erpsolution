<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->integer('general_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->float('unit_price', 12, 2)->nullable();
            $table->float('total_price', 12, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->enum('status', ['Purchase', 'Manual Purchase', 'Production Sale', 'Production', 'Production Out', 'Sale', 'Damage', 'Lost', 'Gain', 'Others', 'Transfer Out', 'Transfer In', 'Project', 'Project In', 'Project Out', 'Project Use', 'Return'])->default('Purchase')->comment('default status set Purchase');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['general_id', 'branch_id', 'product_id']);
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
        Schema::dropIfExists('stocks');
    }
}
