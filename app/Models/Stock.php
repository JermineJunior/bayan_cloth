<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Stock extends Model
{
    protected $fillable = ['name'];

    protected static function booted(): void
    {
        static::addGlobalScope(function (Builder $query) {
            if (session('branch_id')) {
                $query->whereIn('stocks.id', DB::table('branch_stock')->where('branch_id', session('branch_id'))->select('stock_id'));
            }
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_stocks')->withPivot('quantity', 'unit_stock');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_stock');
    }
}
