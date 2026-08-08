<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{

    use HasFactory;

    protected $fillable = ['title', 'description', 'type', 'department', 'expire_date', 'created_by'];

    protected $casts = ['expire_date' => 'date'];

    // Point 8: expire_date chole gele "expired" gonno hobe (update lock korার jonno)
    public function isExpired(): bool
    {
        return $this->expire_date && $this->expire_date->isPast();
    }
}
