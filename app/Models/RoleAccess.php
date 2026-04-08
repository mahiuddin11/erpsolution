<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'role_accesses';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }
}
