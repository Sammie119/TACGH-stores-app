<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialYear extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_active', 'is_closed'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
        'is_closed'  => 'boolean',
    ];

    public static function getActive()
    {
        return static::where('is_active', true)->where('is_closed', false)->first();
    }
}
