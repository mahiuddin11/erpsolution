<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetsWarranty extends Model
{
    use HasFactory;

    protected $guardead = [];

    public function assetList()
    {
        return $this->belongsTo(AssetsList::class, 'assetlist_id', 'id');
    }
}
