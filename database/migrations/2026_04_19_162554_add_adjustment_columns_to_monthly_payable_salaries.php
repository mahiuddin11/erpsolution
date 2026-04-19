<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdjustmentColumnsToMonthlyPayableSalaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monthly_payable_salaries', function (Blueprint $table) {
            //
            $table->decimal('loan_adjustment', 10, 2)->nullable();
            $table->decimal('festival_bonus', 10, 2)->nullable();
            $table->decimal('others_bonus', 10, 2)->nullable();
            $table->decimal('advance_adjustment', 10, 2)->nullable();
            $table->decimal('others_adjustment', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monthly_payable_salaries', function (Blueprint $table) {
            //
            $table->dropColumn([
                'loan_adjustment',
                'festival_bonus',
                'others_bonus',
                'advance_adjustment',
                'others_adjustment'
            ]);
        });
    }
}
