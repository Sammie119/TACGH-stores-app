<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsignmentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'consignment_id', 'product_id', 'quantity', 'unit_price', 'subtotal',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function consignment() { return $this->belongsTo(Consignment::class); }
    public function product()     { return $this->belongsTo(Product::class); }
}
