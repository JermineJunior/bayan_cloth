<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoice extends Model
{
    protected $fillable = ['customer_id', 'invoice_number', 'stock_id', 'invoice_date','sub_total_amount', 'discount_amount', 'tax_amount', 'total_amount', 'payment_status', 'status', 'payment_method', 'cash_id', 'bank_id','cash_amount', 'bank_amount'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'customer_id');
    }

    public function saleInvoiceReturn(): HasOne
    {
        return $this->hasOne(SaleInvoiceReturn::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleInvoiceDetail::class,'sale_invoice_id');
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
