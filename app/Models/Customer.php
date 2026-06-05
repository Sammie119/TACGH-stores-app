<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'balance'];

    protected $casts = ['balance' => 'decimal:2'];

    public function sales() { return $this->hasMany(Sale::class); }
}
