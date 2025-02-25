<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'address', 'phone', 'status'];

    public function scopeStatusActive($query)
    {
        return $query->whereStatus(1);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
}
