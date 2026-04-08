<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'projects';

    public function manager()
    {
        return $this->belongsTo(user::class, 'manager_id', 'id');
    }
    public function project()
    {
        return $this->belongsTo(ProjectMoney::class, 'project_id', 'id');
    }

    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return \Carbon\Carbon::parse($this->start_date)
                ->diffInMonths(\Carbon\Carbon::parse($this->end_date)) . ' Months';
        }

        return '';
    }
}
