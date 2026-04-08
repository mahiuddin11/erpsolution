<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    function scopeAdminUser($builder)
    {
        return $builder->whereNotIn("id", [1]);
    }

    public function manager()
    {
        return $this->belongsTo(Project::class, 'manager_id', 'id');
    }
    public function branch()
    {
        return  $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function employee()
    {
        return  $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'role_id', 'id');
    }
}
