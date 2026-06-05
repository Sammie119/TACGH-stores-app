<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockTake extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference', 'branch_id', 'created_by', 'approved_by',
        'status', 'notes', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('stock_take');
    }

    public function branch()    { return $this->belongsTo(Branch::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy(){ return $this->belongsTo(User::class, 'approved_by'); }
    public function items()     { return $this->hasMany(StockTakeItem::class); }

    public static function generateReference(): string
    {
        $last = static::withTrashed()->latest()->first();
        $next = $last ? ((int) substr($last->reference, 3)) + 1 : 1;
        return 'ST-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    // Summary stats
    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }

    public function getCountedItemsAttribute(): int
    {
        return $this->items()->whereNotNull('counted_quantity')->count();
    }

    public function getVarianceItemsAttribute(): int
    {
        return $this->items()->where('variance', '!=', 0)
            ->whereNotNull('variance')->count();
    }

    public function getTotalVarianceAttribute(): float
    {
        return $this->items()->whereNotNull('variance')->sum('variance');
    }
}
