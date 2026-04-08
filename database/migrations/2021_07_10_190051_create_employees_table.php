<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_id')->unsigned();
            $table->string('name', 100)->nullable();
            $table->integer('id_card')->nullable();
            $table->string('email', 55)->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Cancel'])->default('Active')->comment('default status set active , penidng status waiting for approbal');
            $table->foreignId('user_id')->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('personal_phone')->nullable();
            $table->string('office_phone')->nullable();
            $table->enum('marital_status', ['married', 'unmarried'])->nullable();
            $table->string('nid')->nullable();
            $table->time('last_in_time');
            $table->string('reference')->nullable();
            $table->longText('experience')->nullable();
            $table->longText('present_address')->nullable();
            $table->longText('permanent_address')->nullable();
            $table->string('department')->nullable();
            $table->foreignId('position_id')->nullable();
            $table->longText('achieved_degree')->nullable();
            $table->longText('institution')->nullable();
            $table->text('passing_year')->nullable();
            $table->float('salary')->nullable();
            $table->string('join_date')->nullable();
            $table->string('image')->nullable();
            $table->string('emp_signature')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->enum('over_time_is', ['yes', 'no'])->default('yes');
            $table->string('blood_group')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
