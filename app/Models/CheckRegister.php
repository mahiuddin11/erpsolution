<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckRegister extends Model
{
    use HasFactory;

    protected $table = 'check_registers';

    protected $fillable = [
        'invoice_ref',
        'cheque_no',
        'cheque_date',
        'cleared_date',
        'from_account_id',
        'to_account_id',
        'payee_type',
        'amount',
        'debit',
        'credit',
        'description',
        'status',
        'remark',
        'created_by'
    ];

    public function fromAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
