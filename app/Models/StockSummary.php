<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'branch_id',
        'type',
        'quantity',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'branch_id', 'id');
    }
}
