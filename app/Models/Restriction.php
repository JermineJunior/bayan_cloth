<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restriction extends Model
{
    protected $fillable = ['tree4_id', 'tree4_code', 'madin', 'dain', 'date', 'node', 'constant_number', 'type', 'user_id', 'expense_id', 'revenue_id', 'sale_invoice_id', 'purchase_invoice_id', 'sale_invoice_return_id', 'purchase_invoice_return_id', 'branch_id'];

    public function tree4(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'tree4_id');
    }

    public function sale_invoice()
    {
        return $this->belongsTo(SaleInvoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
