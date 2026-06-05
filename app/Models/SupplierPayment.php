<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SupplierPayment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'reference', 'supplier_id', 'purchase_order_id',
        'paid_by', 'amount', 'payment_method',
        'payment_date', 'bank_reference', 'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('suppliers');
    }

    public function supplier()      { return $this->belongsTo(Supplier::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function paidBy()        { return $this->belongsTo(User::class, 'paid_by'); }

    public static function generateReference(): string
    {
        $last = static::latest()->first();
        $next = $last ? ((int) substr($last->reference, 4)) + 1 : 1;
        return 'PAY-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
