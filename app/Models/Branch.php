<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'branches';

    public function stores()
    {
        return $this->hasMany(Store::class);
    }
    public function fiscal_years()
    {
        return $this->hasMany(FiscalYear::class);
    }
    public function warehouse()
    {
        return $this->hasMany(self::class,"parent_id","id");
    }
}
