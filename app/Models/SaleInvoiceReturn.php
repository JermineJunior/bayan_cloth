<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoiceReturn extends Model
{
    protected $fillable = ['customer_id', 'representative_id', 'user_id', 'date', 'stock_id', 'invoice_number', 'total_amount', 'sub_total_amount', 'tax_amount', 'tax_status', 'discount_amount', 'status', 'bank_name', 'cash_name', 'cash_amount_total', 'bank_amount_total', 'transaction_no', 'process_no', 'deferend_amount', 'branch_id', 'invoice_status', 'rep_percentage', 'rep_cut_amount'];

    public function details(): HasMany
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_invoice_return_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'customer_id');
    }

    public function tree4(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'customer_id');
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'representatvie_id');
    }
}
