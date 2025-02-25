<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    protected $fillable = ['tree4_id', 'tree4_code', 'madin', 'dain', 'date', 'node', 'constant_number', 'type', 'sales_invoice_id', 'user_id','operation_id'];

    public function tree4()
    {
        return $this->belongsTo(Tree4::class, 'tree4_id');
    }
}
