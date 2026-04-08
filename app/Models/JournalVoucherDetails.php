<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherDetails extends Model
{
    use HasFactory;
    protected $guardead = [];
    function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
}
