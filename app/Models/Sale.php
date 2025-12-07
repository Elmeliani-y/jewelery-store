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
        'branch_id',
        'employee_id',
        'category_id',
        'caliber_id',
        'weight',
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
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'total_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'network_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'is_returned' => 'boolean',
        'returned_at' => 'datetime',
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
     * Get the category of the sale.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
        return $this->weight > 0 ? $this->total_amount / $this->weight : 0;
    }

    /**
     * Check if sale price per gram is below minimum threshold from settings.
     */
    public function isBelowMinimumPrice()
    {
        $settingsPath = storage_path('app/private/settings.json');
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $minPrice = (float)($settings['min_invoice_gram_avg'] ?? 0);
        } else {
            $minPrice = (float)config('sales.min_invoice_gram_avg', 0);
        }

        return $minPrice > 0 && $this->price_per_gram < $minPrice;
    }
}