<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnDetail extends Model
{
    protected $fillable = ['sale_invoice_return_id', 'product_id', 'unit_id', 'qty', 'bounce','expire_date', 'price', 'row_sub_total'];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function sale_invoice_return()
    {
        return $this->belongsTo(SaleInvoiceReturn::class);
    }
}
