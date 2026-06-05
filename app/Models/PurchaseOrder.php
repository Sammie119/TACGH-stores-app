<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrder extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'po_number', 'supplier_id', 'branch_id', 'created_by',
        'approved_by', 'order_date', 'expected_date', 'received_date',
        'total_amount', 'amount_paid', 'balance_due',
        'status', 'notes',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'total_amount'  => 'decimal:2',
        'amount_paid'   => 'decimal:2',
        'balance_due'   => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('purchase_orders');
    }

    public function supplier()   { return $this->belongsTo(Supplier::class); }
    public function branch()     { return $this->belongsTo(Branch::class); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function items()      { return $this->hasMany(PurchaseOrderItem::class); }
    public function payments()   { return $this->hasMany(SupplierPayment::class); }

    public static function generatePoNumber(): string
    {
        $last = static::withTrashed()->latest()->first();
        $next = $last ? ((int) substr($last->po_number, 3)) + 1 : 1;
        $prefix = setting('po_prefix', 'TAC');
        return $prefix.'-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
