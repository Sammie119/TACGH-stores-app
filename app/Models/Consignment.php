<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public static function generateReferenceNo(): string
    {
        $last = static::withTrashed()->latest()->first();
        $next = $last ? ((int) substr($last->reference_no, 8)) + 1 : 1;
        return 'TAC-INV-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
