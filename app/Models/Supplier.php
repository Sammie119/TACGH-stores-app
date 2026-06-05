<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'code', 'contact_person', 'phone',
        'email', 'address', 'balance', 'is_active',
    ];

    protected $casts = [
        'balance'   => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('suppliers');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public static function generateCode(): string
    {
        $last = static::withTrashed()->latest()->first();
        $next = $last ? ((int) substr($last->code, 4)) + 1 : 1;
        return 'SUP-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
