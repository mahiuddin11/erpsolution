<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditVoucherDetails extends Model
{
    use HasFactory;

    function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }
}
