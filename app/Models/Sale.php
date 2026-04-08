<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory;
   // use SoftDeletes;

    protected $table = 'sales';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Accounts::class, 'ledger_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(sales_Details::class, 'sale_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
