<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToMonthlyPayableSalariesTable extends Migration
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
            $table->decimal('daily_rate', 10, 2)->default(0)->after('total_salary');
            $table->decimal('absence_deduction', 10, 2)->default(0)->after('employee_absence_day');
            $table->decimal('employee_deducton', 10, 2)->default(0)->after('employee_late');
            $table->integer('holiday')->default(0)->after('employee_paid_leave');
            $table->integer('totalPayableDays')->default(0)->after('holiday');
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
                'daily_rate',
                'absence_deduction',
                'employee_deducton',
                'holiday',
                'totalPayableDays',
            ]);
        });
    }
}
