<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    public $fillable = ['product_id','qty','purchase_order_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
