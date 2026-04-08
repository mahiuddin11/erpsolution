<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidupCapital extends Model
{
    use HasFactory;
    protected $fillable = ['price', 'share'];

}
