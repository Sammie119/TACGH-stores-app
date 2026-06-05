<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sale extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'invoice_no', 'branch_id', 'user_id', 'customer_id',
        'financial_year_id', 'total_amount', 'discount',
        'amount_paid', 'balance_due', 'payment_method', 'status', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount'     => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'balance_due'  => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('sales');
    }

    public function branch()       { return $this->belongsTo(Branch::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function customer()     { return $this->belongsTo(Customer::class); }
    public function financialYear(){ return $this->belongsTo(FinancialYear::class); }
    public function items()        { return $this->hasMany(SaleItem::class); }

    // Auto-generate invoice number
    public static function generateInvoiceNo(): string
    {
        $last = static::latest()->first();
        $next = $last ? ((int) substr($last->invoice_no, 4)) + 1 : 1;
        $prefix = setting('invoice_prefix', 'TAC');
        return $prefix.'-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
