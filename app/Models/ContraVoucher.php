<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContraVoucher extends Model
{
    use HasFactory;
    protected $guardead = [];
    function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    function createdby()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function details()
    {
        return $this->hasMany(ContraVoucherDetails::class, 'contra_voucher_id');
    }
}
