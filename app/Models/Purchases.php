<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchases extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'purchases';

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, "supplier_id",'id');
    }

    public function ledger()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ledger_id');
    }


    public function category()
    {
        return $this->belongsTo(Supplier::class, 'category_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(PurchasesDetails::class, 'purchases_id', 'id');
    }
}
