<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transfer_id', 'product_id',
        'quantity_requested', 'quantity_received',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_received'  => 'decimal:2',
    ];

    public function transfer() { return $this->belongsTo(StockTransfer::class); }
    public function product()  { return $this->belongsTo(Product::class); }
}
