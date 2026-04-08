<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPayableSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'date',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'travel_allowance',
        'food_allowance',
        'total_salary',
        'working_day',
        'employee_presence_day',
        'employee_absence_day',
        'employee_late',
        'employee_paid_leave',
        'employee_unpaid_leave',
        'overtime_houre',
        'overtime_salary',
        'employee_payable_salary',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
