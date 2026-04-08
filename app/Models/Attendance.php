<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['emplyee_id', 'date', 'sign_in', 'sign_out', 'branch_id','latitude','longitude','latitude_out','longitude_out',];

    public function employe()
    {
        return $this->belongsTo(Employee::class, 'emplyee_id',);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
