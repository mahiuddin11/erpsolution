<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('expensecategorie_id')->unsigned();
            $table->integer('expensesubcategorie_id')->unsigned()->nullable();
            $table->integer('branch_id')->unsigned();
            $table->integer('chartofaccount_id')->unsigned();
            $table->date('date')->nullable();
            $table->float('amount', 20, 2)->nullable();
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
