<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOpeningStock extends Model
{
    use HasFactory;

    function user(){
        return $this->belongsTo(User::class,"created_by","id");
    }

    function details(){
        return $this->hasMany(ProductOpeningStockDetails::class,"product_opening_stock_id","id");
    }

    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
