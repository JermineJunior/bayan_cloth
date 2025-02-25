<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'address', 'phone', 'status'];

    public function scopeStatusActive($query)
    {
        return $query->whereStatus(1);
    }
}
