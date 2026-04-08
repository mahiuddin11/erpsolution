<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_setups', function (Blueprint $table) {
            $table->id();
            $table->string('currency')->nullable();
            $table->string('currency_position')->nullable();
            $table->string('language')->nullable();
            $table->time('timezone')->nullable();
            $table->date('dateformat')->nullable();
            $table->string('decimal_separate')->nullable();
            $table->string('thousand_separate')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
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
        Schema::dropIfExists('general_setups');
    }
}
