<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DabitVoucherDetails extends Model
{
    use HasFactory;

    protected $guardead = [];

    function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    function branch(){
       return $this->belongsTo(Branch::class, 'branch_id');
    }

    function project(){
      return  $this->belongsTo(Project::class, 'project_id');
    }
}
