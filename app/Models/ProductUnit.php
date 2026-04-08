<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_units';

 public function products(){
     return $this->hasMany(Product::class, 'unit_id','id');
 }

}
