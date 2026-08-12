<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StockTransfer extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference_no', 'from_branch_id', 'to_branch_id',
        'requested_by', 'approved_by', 'status', 'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('transfers');
    }

    public function fromBranch()  { return $this->belongsTo(Branch::class, 'from_branch_id'); }
    public function toBranch()    { return $this->belongsTo(Branch::class, 'to_branch_id'); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }
    public function items()       { return $this->hasMany(TransferItem::class, 'transfer_id'); }

    // Computes the next number from the true MAX numeric suffix among all
    // rows under the prefix (not "whichever row is latest by created_at")
    // and locks those rows for the duration of the transaction so
    // concurrent requests can't compute the same number.
    public static function generateReferenceNo(): string
    {
        return DB::transaction(function () {
            $prefix = 'TRF-';

            $maxNumber = (int) static::withTrashed()
                ->where('reference_no', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(reference_no, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
                ->value('max_num');

            return $prefix . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
