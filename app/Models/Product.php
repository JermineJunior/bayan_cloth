<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['barcode', 'name', 'name_en', 'category_id', 'reorder_level', 'unit_reorder_level', 'expire_date'];

    protected $casts = [
        'stock_id' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(related: Category::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'product_units')->withPivot('price', 'price_purchase', 'conversion_factor');
    }

    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, 'product_stocks')->withPivot('quantity', 'unit_stock');
    }

    public function convertQuantity($quantity)
    {
        if ($this->units()->count() > 0) {
            $units = $this->units->sortByDesc(fn($unit) => $unit->pivot->conversion_factor);

            // حالة الكمية صفر أو سالبة
            if ($quantity <= 0) {
                $smallestUnit = $this->units->sortBy(fn($unit) => $unit->pivot->conversion_factor)->first();
                return "{$quantity} {$smallestUnit->name}";
            }

            $result = [];

            foreach ($units as $unit) {
                if ($quantity >= $unit->pivot->conversion_factor) {
                    $count = floor($quantity / $unit->pivot->conversion_factor);
                    $quantity = $quantity % $unit->pivot->conversion_factor;
                    $result[] = "$count {$unit->name}";
                }
            }

            return implode(' و ', $result);
        }
    }

    public function stockDetails()
    {
        return $this->hasMany(StockDetail::class, 'product_id');
    }
}
