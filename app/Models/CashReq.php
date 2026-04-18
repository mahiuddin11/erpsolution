<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashReq extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_amount',
        'account_id',
        'recive_account_id',
        'check_number',
        'status',
        'approve_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function approveby()
    {
        return $this->belongsTo(User::class, 'approve_by',"id");
    }

}
