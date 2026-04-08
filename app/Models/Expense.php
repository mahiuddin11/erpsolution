<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['expensecategorie_id', 'expensesubcategorie_id', 'branch_id', 'chartofaccount_id', 'date', 'amount', 'note'];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expensecategorie_id', 'id');
    }
    public function subcategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expensesubcategorie_id', 'id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function chartOfaccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chartofaccount_id', 'id');
    }
}
