<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destroyitems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function assetname()
    {
        return $this->belongsTo(AssetsList::class, 'assetlist_id', 'id');
    }
}
