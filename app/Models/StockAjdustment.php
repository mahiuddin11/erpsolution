<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAjdustment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'stock_ajdustments';

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function details()
    {
        return $this->hasMany(StockAjdustmentDetailst::class, 'purchases_id', 'id');
    }
}
