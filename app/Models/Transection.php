<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'employee_id',
        'branch_id',
        'payment_id',
        'type',
        'date',
        'debit',
        'credit',
        'amount',
        'note',
        'user_id',
        'updated_by',
        'created_by',
        'deleted_by',
    ];

    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
