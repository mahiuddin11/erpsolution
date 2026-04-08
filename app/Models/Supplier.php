<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
  use HasFactory;
  use SoftDeletes;

  public function getBranch()
  {
    return  $this->belongsTo(Branch::class, 'branch_id', 'id');
  }

  function account()
  {
    return $this->morphOne(Accounts::class, "accountable");
  }

  public function amount($productId, $supplier_id)
  {
    return supplierSalePrice::where('product_id', $productId)->where('supplier_id', $supplier_id)->pluck('purchases_price')->first();
  }
}
