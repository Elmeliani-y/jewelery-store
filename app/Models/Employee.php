<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'salary',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the branch that the employee belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the sales for the employee.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope to get only active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total sales for this employee in a given period.
     */
    public function totalSalesInPeriod($startDate = null, $endDate = null)
    {
        $query = $this->sales()->where('is_returned', false);
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return $query->sum('total_amount');
    }
}