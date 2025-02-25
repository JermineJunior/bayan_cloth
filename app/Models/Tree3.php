<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tree3 extends Model
{
    protected $fillable = ['tree3_name', 'tree3_code', 'tree2_id', 'tree2_code'];

    public function tree2(): BelongsTo
    {
        return $this->belongsTo(Tree2::class);
    }

    public function tree4(): HasMany
    {
        return $this->hasMany(Tree4::class);
    }
}
