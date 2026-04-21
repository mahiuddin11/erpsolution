<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpPayDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'lone',
        'amount',
        'payable_salary',
        'bonus_id',
        'total_bonus'

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function bonuses(){
        return $this->hasMany(EmpPayBonus::class, 'emp_pay_details_id' );
    }


}
