<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['barcode', 'name', 'category_id', 'purchase_price', 'reorder_level', 'unit_purchase_price', 'unit_reorder_level', 'status', 'expire_date'];

    protected $casts = [
        'stock_id' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(related: Category::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'product_units')->withPivot('price', 'conversion_factor');
    }

    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, 'product_stocks')->withPivot('quantity', 'unit_stock');
    }

    public function convertQuantity($quantity)
    {
        $result = [];

        // ترتيب الوحدات من الأكبر إلى الأصغر
        $units = $this->units->sortByDesc(fn($unit) => $unit->pivot->conversion_factor);

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
