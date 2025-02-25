<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreliminaryInvoice extends Model
{
    protected $fillable = ['customer_id', 'invoice_date', 'invoice_number', 'stock_id', 'total_amount', 'sub_total_amount', 'tax_amount', 'discount_amount'];

    public function details(): HasMany
    {
        return $this->hasMany(PreliminaryDetail::class,'preliminary_invoice_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'customer_id');
    }
}
