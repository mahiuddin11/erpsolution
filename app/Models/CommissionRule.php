<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'commission_type', 'fixed_percentage', 'min_amount', 'max_amount', 'percentage'];

    public function salesperson()
    {
        return $this->belongsTo(Employee::class,'employee_id','id');
    }
}
