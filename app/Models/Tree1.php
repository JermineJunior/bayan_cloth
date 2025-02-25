<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree1 extends Model
{
    protected $fillable = ['tree1_name', 'tree1_code'];

    public function tree2(): HasMany
    {
        return $this->hasMany(Tree2::class);
    }

    
}
