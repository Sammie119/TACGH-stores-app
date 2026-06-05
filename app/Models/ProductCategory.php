<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProductCategory extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'parent_id', 'name', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('categories');
    }

    // Parent category
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    // Direct children
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('name');
    }

    // All descendants recursively
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Products in this category
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // Get all descendant IDs including self
    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }

    // Scope: only root categories (no parent)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    // Get full path e.g. "Electronics > Phones"
    public function getFullPathAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_path . ' > ' . $this->name;
        }
        return $this->name;
    }

    // Check if this is a root category
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
}
