<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'default_caliber_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the sales for the category.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the default caliber for the category.
     */
    public function defaultCaliber()
    {
        return $this->belongsTo(Caliber::class, 'default_caliber_id');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}