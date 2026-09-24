<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tree4 extends Model
{
    protected $fillable = ['tree4_name', 'tree4_code', 'tree3_id', 'tree3_code', 'status', 'user_id', 'phone', 'email', 'address','representative_id','no_bank'];

    public function tree3(): BelongsTo
    {
        return $this->belongsTo(Tree3::class);
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function representative()
    {
        return $this->belongsTo(Tree4::class,'representative_id');
    }

    public function restrictions()
    {
        return $this->hasMany(Restriction::class);
    }

    public function saleRepresentatives()
    {
        return $this->hasMany(SaleInvoice::class,'representative_id');
    }

    public function saleReturnRepresentatives()
    {
        return $this->hasMany(SaleInvoiceReturn::class,'representative_id');
    }
}
