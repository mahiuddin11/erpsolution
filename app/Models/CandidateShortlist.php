<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateShortlist extends Model
{
    use HasFactory;

    protected $guardead = [];

    public function candiateInfo()
    {
        return $this->belongsTo(CandidateInforamtion::class, 'candidateinfo_id', 'id');
    }
}
