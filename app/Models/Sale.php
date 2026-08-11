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
        'amount_paid', 'balance_due', 'payment_method', 'status', 'notes', 'walkin_name',
        'transaction_reference',
        'split_method_1', 'split_amount_1', 'split_reference_1',
        'split_method_2', 'split_amount_2', 'split_reference_2',
    ];

    protected $casts = [
        'total_amount'   => 'decimal:2',
        'discount'       => 'decimal:2',
        'amount_paid'    => 'decimal:2',
        'balance_due'    => 'decimal:2',
        'split_amount_1' => 'decimal:2',
        'split_amount_2' => 'decimal:2',
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
    public function returns()      { return $this->hasMany(ProductReturn::class); }

    // Sales that involve $method either directly or as one leg of a split payment.
    public function scopePaymentMethod($query, string $method)
    {
        return $query->where(function ($q) use ($method) {
            $q->where('payment_method', $method)
              ->orWhere(function ($q2) use ($method) {
                  $q2->where('payment_method', 'split')
                      ->where(function ($q3) use ($method) {
                          $q3->where('split_method_1', $method)
                              ->orWhere('split_method_2', $method);
                      });
              });
        });
    }

    // Auto-generate invoice number
    public static function generateInvoiceNo(): string
    {
        $last = static::latest()->first();
        $next = $last ? ((int) substr($last->invoice_no, 8)) + 1 : 1;
        $prefix = setting('invoice_prefix', 'TAC');
        return $prefix.'-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
