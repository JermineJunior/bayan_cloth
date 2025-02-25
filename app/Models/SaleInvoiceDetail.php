<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoiceDetail extends Model
{
    protected $fillable = ['sale_invoice_id', 'product_id', 'unit_id', 'qty', 'price', 'row_sub_total'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
