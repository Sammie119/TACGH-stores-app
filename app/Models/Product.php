<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'name', 'sku', 'barcode', 'category_id', 'unit',
        'cost_price', 'selling_price', 'reorder_level',
        'image', 'description', 'is_active',
    ];

    protected $casts = [
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'reorder_level' => 'integer',
        'is_active'     => 'boolean',
    ];

    protected $appends = ['total_quantity'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('products');
    }

    public function category()  { return $this->belongsTo(ProductCategory::class); }
    public function stock()     { return $this->hasMany(BranchStock::class); }
    public function saleItems() { return $this->hasMany(SaleItem::class); }

    public function stockForBranch($branchId)
    {
        return $this->stock()->where('branch_id', $branchId)->first();
    }

    public function getTotalQuantityAttribute()
    {
        return BranchStock::where('product_id', $this->id)->sum('quantity');
    }
}
