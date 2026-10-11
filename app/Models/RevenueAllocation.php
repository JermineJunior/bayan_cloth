<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenueAllocation extends Model
{
    protected $fillable = ['revenue_id', 'sale_invoice_id', 'amount'];

    public function revenue(): BelongsTo
    {
        return $this->belongsTo(Revenue::class);
    }

    public function saleInvoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class);
    }
}
