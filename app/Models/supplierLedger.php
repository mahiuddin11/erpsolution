<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplierLedger extends Model
{
  use HasFactory;
  public function branch()
  {
    return  $this->belongsTo(Branch::class, 'branch_id', 'id');
  }
  public function supplier()
  {
    return  $this->belongsTo(Supplier::class, 'supplier_id', 'id');
  }
  public function accounts()
  {
    return  $this->belongsTo(ChartOfAccount::class, 'account_id', 'id');
  }
}
