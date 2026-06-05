<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'product_id', 'user_id', 'type',
        'quantity', 'balance_after', 'reference_type',
        'reference_id', 'notes',
    ];

    protected $casts = [
        'quantity'      => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function branch()  { return $this->belongsTo(Branch::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function user()    { return $this->belongsTo(User::class); }

    // Helper to record a movement in one line from anywhere
    public static function record(
        int $branchId, int $productId, int $userId,
        string $type, float $quantity, float $balanceAfter,
        string $referenceType = null, int $referenceId = null,
        string $notes = null
    ): void {
        static::create([
            'branch_id'      => $branchId,
            'product_id'     => $productId,
            'user_id'        => $userId,
            'type'           => $type,
            'quantity'       => $quantity,
            'balance_after'  => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'notes'          => $notes,
        ]);
    }
}
