<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{

    use SoftDeletes;

    protected $table = 'sale_returns';

    protected $fillable = [
        'return_no',
        'original_sale_id',
        'customer_id',
        'branch_id',
        'backup_branch_id',
        'return_date',
        'return_type',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'refund_method',
        'refund_account_id',
        'overall_condition',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'return_date'  => 'date',
        'approved_at'  => 'datetime',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    | NOTE: Adjust related model class names/namespaces below to match your
    | actual codebase (e.g. App\Sale vs App\Models\Sale).
    */

    public function saleReturnDetails()
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id');
    }

    public function originalSale()
    {
        return $this->belongsTo(Sale::class, 'original_sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function refundAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'refund_account_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
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
        return $this->return_type === 'full';
    }

    public function requiresCashOutflow(): bool
    {
        return $this->refund_method === 'cash_bank';
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'completed']);
    }
}
