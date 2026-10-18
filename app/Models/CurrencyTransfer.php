<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyTransfer extends Model
{
    protected $fillable = ['date', 'from_account_id', 'to_account_id', 'from_currency_id', 'from_amount', 'to_currency_id', 'to_amount', 'base_amount', 'constant_number', 'note', 'branch_id', 'user_id'];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'to_account_id');
    }

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Curencey::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Curencey::class, 'to_currency_id');
    }
}
