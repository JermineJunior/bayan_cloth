<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curencey extends Model
{
    protected $fillable = ['name', 'amount', 'symbol', 'is_base'];

    protected $casts = [
        'is_base' => 'boolean',
    ];
}
