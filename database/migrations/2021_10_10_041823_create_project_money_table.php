<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_money', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable()->unsigned();
            $table->integer('account_id')->nullable()->unsigned();
            $table->string('projectBananceCode', 15)->unique();
            $table->date('date')->nullable();
            $table->float('debit', 20, 2)->nullable();
            $table->float('credit', 20, 2)->nullable();
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('project_money');
    }
}
