<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unit extends Model
{
    protected $fillable = ['name', 'status'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_units')->withPivot('price');
    }
    public function scopeStatusActive($query)
    {
        return $query->whereStatus(1);
    }
}
