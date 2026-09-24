<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curencey extends Model
{
    protected $fillable = ['name', 'amount', 'symbol'];
}
