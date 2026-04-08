<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContraVoucherDetails extends Model
{
    use HasFactory;

    function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
    function toaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'to_account_id');
    }
}
