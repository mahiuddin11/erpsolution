<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPayableSalary extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'employee_id',
    //     'name',
    //     'date',
    //     'basic_salary',
    //     'house_rent',
    //     'medical_allowance',
    //     'travel_allowance',
    //     'food_allowance',
    //     'total_salary',
    //     'working_day',
    //     'employee_presence_day',
    //     'employee_absence_day',
    //     'employee_late',
    //     'employee_paid_leave',
    //     'employee_unpaid_leave',
    //     'overtime_houre',
    //     'overtime_salary',
    //     'employee_payable_salary',
    //     'status',
    // ];

    protected $fillable = [
        'employee_id',
        'name',
        'date',
        'total_salary',
        'daily_rate',           // ← নতুন
        'employee_presence_day',
        'employee_absence_day',
        'absence_deduction',    // ← নতুন
        'employee_late',
        'employee_deducton',    // ← নতুন
        'employee_paid_leave',
        'holiday',              // ← নতুন
        'totalPayableDays',     // ← নতুন
        'overtime_houre',
        'overtime_salary',
        'lone_adjustment_blance',
        'employee_payable_salary',
        'status',
        'loan_adjustment',
        'festival_bonus',
        'others_bonus',
        'advance_adjustment',
        'others_adjustment',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function bonuses()
    {
        return $this->hasMany(EmpPayBonus::class, 'emp_pay_details_id');
    }
}
