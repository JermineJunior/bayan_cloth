<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreliminaryDetail extends Model
{
    protected $fillable = ['preliminary_invoice_id', 'product_id', 'unit_id', 'qty', 'price', 'row_sub_total'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
