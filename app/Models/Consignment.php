<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Consignment extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'reference_no', 'branch_id', 'user_id', 'customer_id', 'walkin_name',
        'financial_year_id', 'total_value', 'amount_paid', 'balance_due', 'status', 'notes',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('consignments');
    }

    public function branch()        { return $this->belongsTo(Branch::class); }
    public function user()          { return $this->belongsTo(User::class); }
    public function customer()      { return $this->belongsTo(Customer::class); }
    public function financialYear() { return $this->belongsTo(FinancialYear::class); }
    public function items()         { return $this->hasMany(ConsignmentItem::class); }
    public function payments()      { return $this->hasMany(ConsignmentPayment::class); }

    public function getRecipientNameAttribute(): string
    {
        return $this->customer?->name ?? $this->walkin_name ?? 'Walk-in';
    }

    // Computes the next number from the true MAX numeric suffix among all
    // rows under the prefix (not "whichever row is latest by created_at")
    // and locks those rows for the duration of the transaction so
    // concurrent requests can't compute the same number.
    public static function generateReferenceNo(): string
    {
        return DB::transaction(function () {
            $prefix = 'TAC-INV-';

            $maxNumber = (int) static::withTrashed()
                ->where('reference_no', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(reference_no, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
                ->value('max_num');

            return $prefix . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
