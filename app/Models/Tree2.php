<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tree2 extends Model
{
    protected $fillable = ['tree2_name', 'tree2_code', 'tree1_id', 'tree1_code'];

    public function tree1():BelongsTo
    {
        return $this->BelongsTo(Tree1::class);
    }

    public function tree3(): HasMany
    {
        return $this->hasMany(Tree3::class);
    }
}
