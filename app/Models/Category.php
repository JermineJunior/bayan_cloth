<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'branch_id','order'];

    public function products(): HasMany
    {
        return $this->hasMany(related: Product::class);
    }
}
