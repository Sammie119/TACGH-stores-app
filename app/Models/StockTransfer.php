<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public static function generateReferenceNo(): string
    {
        $last = static::latest()->first();
        $next = $last ? ((int) substr($last->reference_no, 4)) + 1 : 1;
        return 'TRF-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
