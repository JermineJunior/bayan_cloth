<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptConfirmation extends Model
{
    public const TYPE_PURCHASE = 'purchase';

    public const TYPE_TRANSFORM = 'transform';

    protected $fillable = [
        'document_type',
        'document_id',
        'user_id',
        'branch_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
