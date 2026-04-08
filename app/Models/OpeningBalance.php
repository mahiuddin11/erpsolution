<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningBalance extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id', 'id');
    }
}
