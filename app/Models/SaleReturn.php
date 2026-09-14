<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sale_returns';

    protected $fillable = [
        'return_no',
        'original_sale_id',
        'branch_id',
        'warehouse_id',
        'ledger_id',
        'sales_person_id',
        'return_date',
        'return_type',
        'grand_total',
        'remarks',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'return_date' => 'date',
        'grand_total' => 'decimal:2',
        'approved_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function details()
    {
        return $this->hasMany(SaleReturnDetails::class, 'sale_return_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'original_sale_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    // Customer track hocche ledger_id die — ChartOfAccount subledger হিসেবে
    public function ledger()
    {
        return $this->belongsTo(ChartOfAccount::class, 'ledger_id');
    }

    public function salesPerson()
    {
        return $this->belongsTo(Employee::class, 'sales_person_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForSale($query, $saleId)
    {
        return $query->where('original_sale_id', $saleId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors / Helpers
    |--------------------------------------------------------------------------
    */

    public function isFullReturn(): bool
    {
        return $this->return_type === 'Full';
    }
}
