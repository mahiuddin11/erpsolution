<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Accounts extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'account_name',
        'accountCode',
        'parent_id',
        'branch_id',
        'status',
        'updated_by',
        'created_by',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
        'depreciation',
        'bill_by_bill',
        'bank_name',
        'accountable_type',
        'accountable_id',
        'opening_balance',
        'balance_type',
        'unique_identifier'  // Add this to fillable
    ];

    protected static function boot()
    {
        parent::boot();
        // Automatically generate a unique identifier before creating the model
        static::creating(function ($model) {
            if (empty($model->unique_identifier)) {
                $model->unique_identifier = $model->generateUniqueIdentifier();
            }

            $type = "debit";

            if(in_array(getFirstAccount($model->parent_id) ?? 0 , [getAccountByUniqueID(9)->id ?? 9,getAccountByUniqueID(17)->id ?? 17])){
                $type = "credit";
            }


            $model->balance_type = $type ;

        });
    }

    /**
     * Generate a unique identifier.
     *
     * @return string
     */
    protected function generateUniqueIdentifier()
    {
        return strtoupper(Str::random(10));
    }

    protected $table = 'chart_of_accounts';

    function accountable()
    {
        return $this->morphTo();
    }

    function parent()
    {
        return $this->belongsTo(Accounts::class, "parent_id", "id");
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function fiscal_years()
    {
        return $this->hasMany(FiscalYear::class);
    }

    public function branch()
    {
        return  $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
