<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['madin_id', 'cach_id', 'bank_id', 'cach_name', 'bank_name', 'note','branch_id'];

    public function cach(): BelongsTo
    {
        return $this->belongsTo(Tree4::class,'cach_id');
    }

    public function bankName(): BelongsTo
    {
        return $this->belongsTo(Tree4::class,'bank_id');
    }

    public function madin()
    {
        return $this->belongsTo(Tree4::class,'madin_id');
    }
}
