<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stock extends Model
{
    protected $fillable = ['name','status'];

    public function scopeStatusActive($query)
    {
        return $query->whereStatus(1);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_stocks')->withPivot('quantity','unit_stock');
    }
}
