<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMoney extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $table = 'project_money';

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'project_id', 'id');
    }
}
