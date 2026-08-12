<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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

    // Computes the next number from the true MAX numeric suffix among all
    // rows under the prefix (not "whichever row is latest by created_at")
    // and locks those rows for the duration of the transaction so
    // concurrent requests can't compute the same number.
    public static function generateReference(): string
    {
        return DB::transaction(function () {
            $prefix = 'ST-';

            $maxNumber = (int) static::withTrashed()
                ->where('reference', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(reference, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
                ->value('max_num');

            return $prefix . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
        });
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
