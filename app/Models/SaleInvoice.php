<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoice extends Model
{
    protected $fillable = ['customer_id', '
    _id', 'representative_id', 'invoice_number', 'date', 'sub_total_amount', 'discount_amount', 'tax_amount', 'tax_status', 'total_amount', 'process_no', 'status', 'deferend_amount', 'cash_name', 'bank_name', 'cash_amount_total', 'bank_amount_total', 'branch_id', 'invoice_status'];

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
        return $this->belongsTo(Tree4::class, 'representative_id');
    }

    public function saleInvoiceReturn(): HasOne
    {
        return $this->hasOne(SaleInvoiceReturn::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleInvoiceDetail::class, 'sale_invoice_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }

    public function returnInvoice(): HasOne
    {
        return $this->hasOne(SaleInvoiceReturn::class, 'sale_invoice_id');
    }

    
}
