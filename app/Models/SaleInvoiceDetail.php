<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoiceDetail extends Model
{
    protected $fillable = ['sale_invoice_id', 'product_id','stock_id', 'unit_id', 'qty', 'bounce','expire_date', 'price', 'row_sub_total'];

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

    public function sale_invoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class);
    }
}
