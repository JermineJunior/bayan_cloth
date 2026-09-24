<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreliminaryInvoice extends Model
{
    protected $fillable = ['customer_id', 'representative_id', 'date', 'invoice_number', 'total_amount', 'sub_total_amount', 'tax_amount','tax_status', 'discount_amount','branch_id'];

    public function details(): HasMany
    {
        return $this->hasMany(PreliminaryDetail::class, 'preliminary_invoice_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'customer_id');
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'representative_id');
    }
}
