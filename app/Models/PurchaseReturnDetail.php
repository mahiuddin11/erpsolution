<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_return_details';

    protected $fillable = [
        'purchase_return_id',
        'purchase_detail_id',
        'product_id',
        'purchasetype',
        'returned_qty',
        'unit_price',
        'line_amount',
        'reason',
    ];

    protected $casts = [
        'returned_qty' => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'line_amount'  => 'decimal:2',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchasesDetails::class, 'purchase_detail_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

