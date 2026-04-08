<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function conversion()
    {
        return $this->belongsTo(Conversion::class, 'conversion_id', 'id');
    }

    public function saleqty()
    {
        return $this->belongsTo(Conversion::class, 'conversion_id', 'id');
    }
}
