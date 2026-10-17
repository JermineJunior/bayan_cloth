<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoice extends Model
{
    protected $fillable = ['supplier_id', 'representative_id', 'invoice_number', 'stock_id', 'date', 'sub_total_amount', 'discount_amount', 'tax_amount', 'tax_status', 'total_amount', 'status', 'cash_name', 'bank_name', 'cash_amount_total', 'bank_amount_total', 'process_no','deferend_amount','transaction_no','branch_id','invoice_status','user_id','note'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Tree4::class , 'supplier_id');
    }

    public function tree4(): BelongsTo
    {
        return $this->belongsTo(Tree4::class , 'supplier_id');
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
    
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceDetail::class,'purchase_invoice_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }

    public function returnInvoice()
    {
        return $this->hasOne(PurchaseInvoiceReturn::class);
    }

}
