<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transform_details extends Model
{
    protected $fillable = ['transform_id', 'product_id', 'stock_id_from', 'stock_id_to', 'quantity', 'unit_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockFrom(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id_from');
    }

    public function stockTo(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id_to');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
