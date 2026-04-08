<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUse extends Model
{
    use HasFactory;


    public function project()
    {
        return $this->belongsTo(project::class, 'project_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'create_by', 'id');
    }
}
