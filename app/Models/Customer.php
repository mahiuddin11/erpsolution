<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customergroup_id', 'id');
    }

    function account()
    {
      return $this->morphOne(Accounts::class, "accountable");
    }
}
