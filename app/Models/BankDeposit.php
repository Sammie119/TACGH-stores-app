<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BankDeposit extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'branch_id', 'deposited_by', 'verified_by', 'amount',
        'slip_image', 'deposit_date', 'bank_name',
        'reference_no', 'status', 'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'deposit_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('banking');
    }

    public function branch()      { return $this->belongsTo(Branch::class); }
    public function depositedBy() { return $this->belongsTo(User::class, 'deposited_by'); }
    public function verifiedBy()  { return $this->belongsTo(User::class, 'verified_by'); }
}
