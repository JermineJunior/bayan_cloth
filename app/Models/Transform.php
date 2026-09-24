<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transform extends Model
{
    protected $fillable = ['stock_from', 'stock_to', 'branch_id'];

    public function fromStock()
    {
        return $this->belongsTo(Stock::class, 'stock_from');
    }

    public function toStock()
    {
        return $this->belongsTo(Stock::class, 'stock_to');
    }

    public function transformDetails()
    {
        return $this->hasMany(Transform_details::class);
    }
}
