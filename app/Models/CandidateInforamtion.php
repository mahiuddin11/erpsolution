<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateInforamtion extends Model
{
    use HasFactory;

    protected $guardead = [];

    public function eduInfo()
    {
        return $this->hasMany(EducationInfo::class, 'candidateinfo_id', 'id');
    }

    public function workInfo()
    {
        return $this->hasMany(WorkExpInfo::class, 'candidateinfo_id', 'id');
    }
}
