<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'expense_type_id',
        'description',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the branch that the expense belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the expense type.
     */
    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    /**
     * Scope to get expenses in date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get expenses by branch.
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to get expenses by type.
     */
    public function scopeByType($query, $typeId)
    {
        return $query->where('expense_type_id', $typeId);
    }
}