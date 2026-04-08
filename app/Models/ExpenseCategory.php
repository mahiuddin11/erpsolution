<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'expense_categories';

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'categorie_id', 'id');
    }
}
