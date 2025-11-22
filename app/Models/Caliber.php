<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caliber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tax_rate',
        'is_active',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the sales for the caliber.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope to get only active calibers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate tax amount for a given total.
     */
    public function calculateTax($totalAmount)
    {
        return ($totalAmount * $this->tax_rate) / 100;
    }

    /**
     * Calculate net amount after tax.
     */
    public function calculateNetAmount($totalAmount)
    {
        return $totalAmount - $this->calculateTax($totalAmount);
    }
}