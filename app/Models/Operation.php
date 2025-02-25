<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operation extends Model
{
    protected $fillable = ['madin_id', 'dain_id', 'madin', 'dain', 'price', 'constant_number', 'node', 'sale_invoice_id', 'purchase_invoice_id', 'sale_invoice_return_id', 'purchase_invoice_return_id', 'type', 'user_id'];

    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tree4_madin()
    {
        return $this->belongsTo(Tree4::class,'madin_id','id');
    }

    public function tree4_dain()
    {
        return $this->belongsTo(Tree4::class,'dain_id','id');
    }
}
