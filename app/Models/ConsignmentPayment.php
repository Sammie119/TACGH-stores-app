<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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

    // Computes the next number from the true MAX numeric suffix among all
    // rows under the prefix (not "whichever row is latest by created_at")
    // and locks those rows for the duration of the transaction so
    // concurrent requests can't compute the same number.
    public static function generateReference(): string
    {
        return DB::transaction(function () {
            $prefix = 'CPAY-';

            $maxNumber = (int) static::withTrashed()
                ->where('reference', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(reference, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
                ->value('max_num');

            return $prefix . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
