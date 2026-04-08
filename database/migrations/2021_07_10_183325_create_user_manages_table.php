<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserManagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_manages', function (Blueprint $table) {
            $table->id();
            $table->string('firstname',100)->nullable();
            $table->string('lastname',100)->nullable();
            $table->string('email',50)->nullable();
            $table->string('phone',20)->nullable();
            $table->integer('branch_id')->unsigned();
            $table->integer('status_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->index(['branch_id', 'status_id','role_id']);
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
        Schema::dropIfExists('user_manages');
    }
}
