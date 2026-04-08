<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_returns', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('invoice_no')->nullable();
            $table->integer('project_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('stock_total')->nullable();
            $table->integer('return_total')->nullable();
            $table->enum('status', ['Pending', 'Approve', 'Cancel'])->default('Pending');
            $table->text('note')->nullable();
            $table->integer('create_by')->nullable();
            $table->integer('update_by')->nullable();
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
        Schema::dropIfExists('project_returns');
    }
}
