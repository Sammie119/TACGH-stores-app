<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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

    // Computes the next number from the true MAX numeric suffix among all
    // rows under the current prefix (not "whichever row is latest by
    // created_at") and locks those rows for the duration of the
    // transaction so concurrent requests can't compute the same number.
    public static function generatePoNumber(): string
    {
        return DB::transaction(function () {
            $prefix = setting('po_prefix', 'TAC') . '-';

            $maxNumber = (int) static::withTrashed()
                ->where('po_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(po_number, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
                ->value('max_num');

            return $prefix . str_pad($maxNumber + 1, 6, '0', STR_PAD_LEFT);
        });
    }
}
