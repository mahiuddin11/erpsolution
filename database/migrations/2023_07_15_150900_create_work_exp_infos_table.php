<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkExpInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_exp_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidateinfo_id');
            $table->string('company_name')->nullable();
            $table->string('experience')->nullable();
            $table->string('supervisor')->nullable();
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
        Schema::dropIfExists('work_exp_infos');
    }
}
