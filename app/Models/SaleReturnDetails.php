<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnDetails extends Model
{
    use HasFactory;

    protected $table = 'sale_return_details';

    protected $fillable = [
        'sale_return_id',
        'sale_detail_id',
        'product_id',
        'returned_qty',
        'unit_price',
        'vat_percent',
        'line_amount',
        'condition',
        'reason',
    ];

    protected $casts = [
        'returned_qty' => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'vat_percent'  => 'decimal:2',
        'line_amount'  => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
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

    public function goesBackToStock(): bool
    {
        return $this->condition === 'good';
    }
}
