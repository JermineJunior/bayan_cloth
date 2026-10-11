<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Revenue extends Model
{
    protected $fillable = ['dain_id', 'cach_id', 'bank_id', 'cach_name', 'bank_name', 'transaction_no', 'note','branch_id', 'representative_id', 'rep_percentage', 'rep_cut_amount'];

    public function allocations(): HasMany
    {
        return $this->hasMany(RevenueAllocation::class);
    }

    public function cach(): BelongsTo
    {
        return $this->belongsTo(Tree4::class,'cach_id');
    }

    public function bankName(): BelongsTo
    {
        return $this->belongsTo(Tree4::class,'bank_id');
    }

    public function dain()
    {
        return $this->belongsTo(Tree4::class,'dain_id');
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Tree4::class, 'representative_id');
    }
}
