<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseInvoiceReturn extends Model
{
    protected $fillable = ['supplier_id', 'purchase_invoice_id', 'purchase_invoice_number', 'invoice_date', 'invoice_number', 'total_amount', 'sub_total_amount', 'tax_amount', 'discount_amount', 'payment_status', 'status', 'payment_method', 'bank_id', 'cash_id', 'cash_amount', 'bank_amount'];

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class,'purchase_invoice_return_id');
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
