<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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

    // Computes the next number from the true MAX numeric suffix among all
    // rows under the prefix (not "whichever row is latest by created_at")
    // and locks those rows for the duration of the transaction so
    // concurrent requests can't compute the same number.
    public static function generateCode(): string
    {
        return DB::transaction(function () {
            $prefix = 'SUP-';

            $maxNumber = (int) static::withTrashed()
                ->where('code', 'like', $prefix . '%')
                ->lockForUpdate()
                ->selectRaw('MAX(CAST(SUBSTRING(code, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
                ->value('max_num');

            return $prefix . str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}
