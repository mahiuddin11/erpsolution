<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetsList extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function assetCategory()
    {
        return $this->belongsTo(AssetsCategory::class, 'category_asset_id', 'id');
    }
}
