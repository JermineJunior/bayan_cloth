<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->belongsToMany(User::class,'branch_user', 'branch_id', 'user_id');
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'branch_stock');
    }
}
