<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpPayDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_pay_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pay_sheet_id');
            $table->foreignId('branch_id');
            $table->foreignId('employee_id');
            $table->double('payble_salary', 10, 2);
            $table->double('amount', 10, 2);
            $table->double('lone', 10, 2)->nullable();
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
        Schema::dropIfExists('emp_pay_details');
    }
}
