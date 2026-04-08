<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;


    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'categorie_id', 'id');
    }
    public function subCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'subcategorie_id', 'id');
    }
}
