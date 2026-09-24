<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockDetail extends Model
{
    protected $fillable = ['product_id','type','user_id','wared','sader','unit_id','stock_id','sale_invoice_id','purchase_invoice_id','sale_invoice_return_id','purchase_invoice_return_id','branch_id','transform_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale_invoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class);
    }

    public function purchase_invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function sale_invoice_return(): BelongsTo
    {
        return $this->belongsTo(SaleInvoiceReturn::class);
    }

    public function purchase_invoice_return(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoiceReturn::class);
    }
}
