<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ConsignmentPayment extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference', 'consignment_id', 'paid_by', 'amount',
        'payment_method', 'payment_date', 'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('consignments');
    }

    public function consignment() { return $this->belongsTo(Consignment::class); }
    public function paidBy()      { return $this->belongsTo(User::class, 'paid_by'); }

    public static function generateReference(): string
    {
        $last = static::withTrashed()->latest()->first();
        $next = $last ? ((int) substr($last->reference, 5)) + 1 : 1;
        return 'CPAY-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
