<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $fillable = [
        'stock_take_id', 'product_id',
        'system_quantity', 'counted_quantity',
        'variance', 'notes',
    ];

    protected $casts = [
        'system_quantity'  => 'decimal:2',
        'counted_quantity' => 'decimal:2',
        'variance'         => 'decimal:2',
    ];

    public function stockTake() { return $this->belongsTo(StockTake::class); }
    public function product()   { return $this->belongsTo(Product::class); }
}
