<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customerLedger extends Model
{
    use HasFactory;

    public function accounts()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function customer()
    {
        return  $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
