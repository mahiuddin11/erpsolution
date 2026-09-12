<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnDetails extends Model
{

    protected $table = 'sale_return_details';

    protected $fillable = [
        'sale_return_id',
        'sale_detail_id',
        'product_id',
        'original_qty',
        'already_returned_qty',
        'returned_qty',
        'unit_price',
        'line_tax',
        'line_discount',
        'line_total',
        'condition',
        'stock_action',
        'reason',
    ];

    protected $casts = [
        'original_qty'          => 'decimal:2',
        'already_returned_qty'  => 'decimal:2',
        'returned_qty'          => 'decimal:2',
        'unit_price'            => 'decimal:2',
        'line_tax'              => 'decimal:2',
        'line_discount'         => 'decimal:2',
        'line_total'            => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    | NOTE: Adjust related model class names/namespaces below to match your
    | actual codebase (e.g. App\SaleDetail vs App\Models\SaleDetail).
    */

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id');
    }

    public function saleDetail()
    {
        return $this->belongsTo(sales_Details::class, 'sale_detail_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function remainingReturnableQty(): float
    {
        return (float) $this->original_qty - (float) $this->already_returned_qty;
    }

    public function isFullLineReturn(): bool
    {
        return bccomp((string) $this->returned_qty, (string) $this->original_qty, 2) === 0;
    }

    public function goesBackToStock(): bool
    {
        return $this->condition === 'good' && $this->stock_action === 'restock';
    }
}
