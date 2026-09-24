<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceReturn extends Model
{
    protected $fillable = ['supplier_id', 'user_id', 'date', 'invoice_number', 'stock_id', 'total_amount', 'sub_total_amount', 'tax_amount', 'tax_status', 'discount_amount', 'status', 'bank_name', 'cash_name', 'cash_amount_total', 'bank_amount_total', 'process_no', 'deferend_amount','branch_id', 'invoice_status'];

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class, 'purchase_invoice_return_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Tree4::class,'supplier_id');
    }

    public function tree4(): BelongsTo
    {
        return $this->belongsTo(Tree4::class,'supplier_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }
}
