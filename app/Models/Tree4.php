<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tree4 extends Model
{
    protected $fillable = ['tree4_name', 'tree4_code', 'tree3_id', 'tree3_code', 'status', 'user_id'];

    public function tree3(): BelongsTo
    {
        return $this->belongsTo(Tree3::class);
    }
}
