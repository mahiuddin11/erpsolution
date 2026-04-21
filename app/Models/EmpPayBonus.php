<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpPayBonus extends Model
{
    use HasFactory;

    protected $table = 'emp_pay_bonus_details';

    const BONUS_TYPES = [
        'eid_ul_fitr'        => 'Eid ul Fitr',
        'eid_ul_adha'        => 'Eid ul Adha',
        'others'      => 'Others',
        'performance' => 'Performance Bonus',
       
    ];

    protected $fillable = [
        'emp_pay_details_id',
        'employee_id',
        'monthly_payable_salaries_id',
        'bonus_type',
        'bonus_amount',
        'remarks'
    ];

    public function payDetail(){
        return $this->belongsTo(EmpPayDetails::class, 'emp_pay_details_id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function monthlyPayableSalary(){
        return $this->belongTo(MonthlyPayableSalary::class, 'monthly_payable_salaries_id');
    }

    public function getBonusType()
    {
        return self::BONUS_TYPES[$this->bonus_type] ?? ucfirst($this->bonus_type);
    }

    public static function getBonusTypes()
    {
        return self::BONUS_TYPES;
    }


}
