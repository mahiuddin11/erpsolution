<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    use HasFactory;

    protected $table = 'loan_details';

    protected $fillable = [
        'lone_id',
        'employee_id',
        'month',
        'amount',
        'status',
        'note'
    ];

    protected $casts = [
        'month' => 'date',
        'amount' => 'float',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function lone()
    {
        return $this->belongsTo(Lone::class, 'lone_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
