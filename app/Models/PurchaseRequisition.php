<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function approved()
    {
        return $this->belongsTo(User::class, 'approve_by', 'id');
    }

    public function details()
    {
        return $this->hasMany(PrDetails::class, 'pr_id', 'id');
    }
}
