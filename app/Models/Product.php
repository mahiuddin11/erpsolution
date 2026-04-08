<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'products';
    public $attributeSkip = false;
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id', 'id');
    }

    public function subproduct()
    {
        return $this->hasMany(self::class, 'parent_id',);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id',);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getRawNameAttribute()
    {
        return $this->attributes['name']; // Directly fetch from database
    }

    public function getNameAttribute($value)
    {
        if (!$this->attributeSkip) {
            $parent = $this->parent->name ?? "";
            $brand = $this->brand->name ?? null;
            $modify = $brand ? "($brand)" : "";
            return " $modify  $parent  $value ";
        }
        return $value;
    }
}
