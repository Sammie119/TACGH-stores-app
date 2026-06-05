<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    public $timestamps = false;

    protected $table = 'branch_stock';

    protected $fillable = ['branch_id', 'product_id', 'quantity', 'last_updated'];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    public function branch()  { return $this->belongsTo(Branch::class); }
    public function product() { return $this->belongsTo(Product::class); }

    // Increase stock quantity
    public function incrementStock(float $qty): void
    {
        $this->quantity += $qty;
        $this->last_updated = now();
        $this->save();
    }

    // Decrease stock quantity
    public function decrementStock(float $qty): void
    {
        $this->quantity = max(0, $this->quantity - $qty);
        $this->last_updated = now();
        $this->save();
    }
}
