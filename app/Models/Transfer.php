<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfers';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function frombranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id', 'id');
    }
    public function tobranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(TransferDetails::class, 'transfer_id', 'id');
    }
}
