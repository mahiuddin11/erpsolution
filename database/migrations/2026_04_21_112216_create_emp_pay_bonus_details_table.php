<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpPayBonusDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_pay_bonus_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emp_pay_details_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('monthly_payable_salaries_id');
            $table->string('bonus_type');                    // fitr, adha, others, performance, etc.
            $table->decimal('bonus_amount', 15, 2)->default(0.00);
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('emp_pay_bonus_details');
    }
}
