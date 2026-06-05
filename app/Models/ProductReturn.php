<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductReturn extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'returns';

    protected $fillable = [
        'sale_id', 'product_id', 'branch_id', 'quantity',
        'refund_amount', 'reason', 'type', 'status', 'processed_by',
    ];

    protected $casts = [
        'quantity'      => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('returns');
    }

    public function sale()        { return $this->belongsTo(Sale::class); }
    public function product()     { return $this->belongsTo(Product::class); }
    public function branch()      { return $this->belongsTo(Branch::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }
}
