<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;

    function category()
    {
        return $this->belongsTo(Self::class, 'parent_id');
    }
}
