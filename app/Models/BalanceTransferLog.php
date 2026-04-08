<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceTransferLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    public function from_account()
    {
        return $this->belongsTo(Accounts::class, 'from_account_id', 'id');
    }
    public function to_account()
    {
        return $this->belongsTo(Accounts::class, 'to_account_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}