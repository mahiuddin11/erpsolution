<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_return_details', function (Blueprint $table) {
            $table->id();
            $table->integer('project_return_id')->unsigned();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('stock_qty')->nullable();
            $table->integer('return_qty')->nullable();
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
        Schema::dropIfExists('project_return_details');
    }
}
