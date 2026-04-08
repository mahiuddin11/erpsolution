<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyPayableSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_payable_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->double('basic_salary', 11, 2)->nullable();
            $table->double('house_rent', 11, 2)->nullable();
            $table->double('medical_allowance', 11, 2)->nullable();
            $table->double('travel_allowance', 11, 2)->nullable();
            $table->double('food_allowance', 11, 2)->nullable();
            $table->double('total_salary', 11, 2)->nullable();
            $table->integer('working_day')->nullable();
            $table->integer('employee_presence_day')->nullable();
            $table->integer('employee_absence_day')->nullable();
            $table->string('employee_late')->nullable();
            $table->integer('employee_paid_leave')->nullable();
            $table->integer('employee_unpaid_leave')->nullable();
            $table->string('overtime_houre')->nullable();
            $table->double('overtime_salary', 11, 2)->nullable();
            $table->double('employee_payable_salary', 11, 2)->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
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
        Schema::dropIfExists('monthly_payable_salaries');
    }
}
