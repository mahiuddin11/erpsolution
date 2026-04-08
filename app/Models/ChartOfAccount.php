<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ChartOfAccount extends Model
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

        // Automatically generate a unique identifier and add balance_type before creating the model
        static::creating(function ($model) {
            if (empty($model->unique_identifier)) {
                $model->unique_identifier = $model->generateUniqueIdentifier();
            }

            $type = "debit";

            if (in_array(getFirstAccount($model->parent_id) ?? 0, [getAccountByUniqueID(9)->id ?? 9, getAccountByUniqueID(17)->id ?? 17])) {
                $type = "credit";
            }


            $model->balance_type = $type;
        });
    }

    function accountable()
    {
        return $this->morphTo();
    }

    /**
     * Generate a unique identifier.
     *
     * @return string
     */
    protected function generateUniqueIdentifier()
    {
        // Customize the identifier generation logic here
        return strtoupper(Str::random(10));
    }

    public function subAccount()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // public function getTypeOfAccount($id = [], $oldIds = [])
    // {

    //     $ids = Self::whereIn('parent_id', $id)->pluck('id')->toArray();
    //     if ($ids) {
    //         $marge = array_merge($ids, $oldIds);
    //         return Self::getTypeOfAccount($ids, $marge);
    //     }
    //     return $oldIds;
    // }

    public static function getTypeOfAccount($ids, $oldIds = [])
    {
        $ids = self::whereIn('parent_id', $ids)->pluck('id')->toArray();
        if (!empty($ids)) {
            $marge = array_merge($ids, $oldIds);
            return self::getTypeOfAccount($ids, $marge);
        }
        return $oldIds;
    }

    function parent()
    {
        return $this->belongsTo(Accounts::class, "parent_id", "id");
    }

    public static  function getaccount($id = null)
    {
        $id = getAccountByUniqueID($id)->id;
        $account_list =  Self::where('status', 'Active');
        if ($id) {
            $account_list = $account_list->whereIn('id', self::getTypeOfAccount([$id]))->orWhereIn("id", [$id]);
        }
        // $account_list = $account_list->where('company_id', auth()->user()->company_id);
        return $account_list;
    }

    //new added
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->where('status', 1)
            ->with('children');
    }
}
