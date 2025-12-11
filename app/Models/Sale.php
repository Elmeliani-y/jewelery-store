<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'products',
        'branch_id',
        'employee_id',
        'caliber_id',
        // ...existing code...
        'total_amount',
        'cash_amount',
        'network_amount',
        'network_reference',
        'payment_method',
        'tax_amount',
        'net_amount',
        'notes',
        'is_returned',
        'returned_at',
        'customer_received',
    ];

    protected $casts = [
        'products' => 'array',
        // ...existing code...
        'total_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'network_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'is_returned' => 'boolean',
        'returned_at' => 'datetime',
        'customer_received' => 'boolean',
    ];

    /**
     * Get the branch that the sale belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the employee that made the sale.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the caliber of the sale.
     */
    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }

    /**
     * Scope to get only non-returned sales.
     */
    public function scopeNotReturned($query)
    {
        return $query->where('is_returned', false);
    }

    /**
     * Scope to get sales in date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Scope to get sales by branch.
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to get sales by employee.
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to get sales by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get sales by caliber.
     */
    public function scopeByCaliber($query, $caliberId)
    {
        return $query->where('caliber_id', $caliberId);
    }

    /**
     * Generate unique invoice number.
     */
    public static function generateInvoiceNumber()
    {
        $lastSale = self::orderBy('id', 'desc')->first();
        $number = $lastSale ? $lastSale->id + 1 : 1;
        return (string) $number; // Simple sequential: 1, 2, 3...
    }

    /**
     * Return this sale.
     */
    public function returnSale()
    {
        $this->update([
            'is_returned' => true,
            'returned_at' => now(),
        ]);
    }

    /**
     * Calculate and set tax and net amounts based on caliber.
     */
    public function calculateAmounts()
    {
        if ($this->caliber) {
            $this->tax_amount = $this->caliber->calculateTax($this->total_amount);
            $this->net_amount = $this->total_amount - $this->tax_amount;
        }
    }

    /**
     * Get price per gram.
     */
    public function getPricePerGramAttribute()
    {
        // Removed direct weight usage; use products array for calculations in controllers
        return null;
    }

    /**
     * Check if sale price per gram is below minimum threshold from settings.
     */
    public function isBelowMinimumPrice()
    {
        $minPrice = (float)\App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 0));
        return $minPrice > 0 && $this->price_per_gram < $minPrice;
    }
}