<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\Branch;
use App\Models\Caliber;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Removed device validation from constructor; now enforced at the start of every action.
    private function validateDeviceOrAbort()
    {
        $token = request()->cookie('device_token');
        if ($token) {
            $device = \App\Models\Device::where('token', $token)->first();
            if (! $device || ! $device->active || ! $device->user_id || ! \App\Models\User::where('id', $device->user_id)->exists()) {
                \Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                \Cookie::queue(\Cookie::forget('device_token'));
                abort(404);
            }
        }
    }
    /**
     * Accounts report: show network, cash, transfer by branch and date range
     */
    public function accounts(Request $request)
    {
        $this->validateDeviceOrAbort();
        $this->enforceDeviceOrAdminOr404($request);
        $branches = Branch::active()->get();
        $branchId = $request->get('branch_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $summary = null;
        $debug = null;
        $allSales = \App\Models\Sale::all();
        // If branch/date not selected, show all sales as 'sales' for easier debugging
        if ($branchId && $dateFrom && $dateTo) {
            $sales = Sale::byBranch($branchId)
                ->inDateRange($dateFrom, $dateTo)
                ->where(function ($q) {
                    $q->where('is_returned', false)
                        ->orWhere('is_returned', 0)
                        ->orWhere('is_returned', '0')
                        ->orWhereNull('is_returned');
                })
                ->get();
            $returns = Sale::where('is_returned', true)
                ->byBranch($branchId)
                ->whereBetween('returned_at', [$dateFrom, $dateTo])
                ->get();
        } else {
            $sales = $allSales;
            $returns = collect();
        }
        $network = 0;
        $cash = 0;
        $transfer = 0;
        foreach ($sales as $sale) {
            if ($sale->payment_method === 'network') {
                $network += $sale->network_amount ?? $sale->total_amount ?? 0;
            } elseif ($sale->payment_method === 'cash') {
                $cash += $sale->cash_amount ?? $sale->total_amount ?? 0;
            } elseif ($sale->payment_method === 'transfer') {
                $transfer += $sale->total_amount ?? 0;
            }
        }
        foreach ($returns as $sale) {
            if ($sale->payment_method === 'network') {
                $network -= $sale->network_amount ?? $sale->total_amount ?? 0;
            } elseif ($sale->payment_method === 'cash') {
                $cash -= $sale->cash_amount ?? $sale->total_amount ?? 0;
            } elseif ($sale->payment_method === 'transfer') {
                $transfer -= $sale->total_amount ?? 0;
            }
        }
        $summary = [
            'network' => $network,
            'cash' => $cash,
            'transfer' => $transfer,
        ];
        $debug = [
            'sales' => $sales,
            'returns' => $returns,
            'allSales' => $allSales,
        ];

        return view('reports.accounts', compact('branches', 'summary', 'debug'));

        return view('reports.accounts', compact('branches', 'summary'));
    }

    /**
     * Comparative by periods report (period comparison).
     * Accepts two date ranges: from1/to1 and from2/to2, a branch_id and a period type filter.
     */
    public function periodComparison(Request $request)
    {
        $this->validateDeviceOrAbort();
        $branches = Branch::active()->get();

        // Inputs: from1/to1, from2/to2, branch_id, period_type
        $periodType = $request->get('period_type', 'annual'); // annual|monthly|weekly|special
        $branchId = $request->get('branch_id');
        $from1 = $request->get('from1');
        $to1 = $request->get('to1');
        $from2 = $request->get('from2');
        $to2 = $request->get('to2');
        // For annual mode, prefer from1_year/from2_year if present
        $from1_year = $request->get('from1_year');
        $from2_year = $request->get('from2_year');

        // Set defaults if not provided
        $now = Carbon::now();
        if (! $periodType) {
            $periodType = 'annual';
        }
        if (! $branchId) {
            $branchId = null;
        }

        if ($periodType === 'annual') {
            $thisYear = $now->year;
            $lastYear = $now->copy()->subYear()->year;
            $from1_val = $from1_year ?: $from1;
            $from2_val = $from2_year ?: $from2;
            $to1_val = $from1_year ?: $to1;
            $to2_val = $from2_year ?: $to2;
            $from1_val = $from1_val ?: $thisYear;
            $to1_val = $to1_val ?: $thisYear;
            $from2_val = $from2_val ?: $lastYear;
            $to2_val = $to2_val ?: $lastYear;
            // Convert to full date ranges
            $from1_date = Carbon::create($from1_val, 1, 1);
            $to1_date = Carbon::create($to1_val, 12, 31);
            $from2_date = Carbon::create($from2_val, 1, 1);
            $to2_date = Carbon::create($to2_val, 12, 31);
        } elseif ($periodType === 'monthly') {
            $thisYear = $now->year;
            $thisMonth = $now->month;
            $lastYear = $now->copy()->subYear()->year;
            $from1 = $from1 ?: ($thisYear.'-'.str_pad($thisMonth, 2, '0', STR_PAD_LEFT).'-01');
            $to1 = $to1 ?: ($thisYear.'-'.str_pad($thisMonth, 2, '0', STR_PAD_LEFT).'-'.Carbon::create($thisYear, $thisMonth, 1)->endOfMonth()->format('d'));
            $from2 = $from2 ?: ($lastYear.'-'.str_pad($thisMonth, 2, '0', STR_PAD_LEFT).'-01');
            $to2 = $to2 ?: ($lastYear.'-'.str_pad($thisMonth, 2, '0', STR_PAD_LEFT).'-'.Carbon::create($lastYear, $thisMonth, 1)->endOfMonth()->format('d'));
            // Accept Y-m-d directly
            $from1_date = Carbon::parse($from1);
            $to1_date = Carbon::parse($to1);
            $from2_date = Carbon::parse($from2);
            $to2_date = Carbon::parse($to2);
        } elseif ($periodType === 'weekly') {
            // Use week of selected date (show week range for the selected day)
            $from1 = $from1 ?: $now->toDateString();
            $from2 = $from2 ?: $now->copy()->subYear()->toDateString();
            $from1_date = Carbon::parse($from1)->startOfWeek();
            $to1_date = Carbon::parse($from1)->endOfWeek();
            $from2_date = Carbon::parse($from2)->startOfWeek();
            $to2_date = Carbon::parse($from2)->endOfWeek();
        } else {
            // Special: use as is (full date pickers)
            $from1_date = $from1 ? Carbon::parse($from1) : null;
            $to1_date = $to1 ? Carbon::parse($to1) : null;
            $from2_date = $from2 ? Carbon::parse($from2) : null;
            $to2_date = $to2 ? Carbon::parse($to2) : null;
        }

        // helper to compute totals for a range, with pretty period label
        $computeTotals = function ($start, $end, $branchId = null) use ($periodType) {

            $result = [
                'period' => ($start && $end) ? ($start.' - '.$end) : 'N/A',
                'total_sales' => 0,
                'total_weight' => 0,
                'price_per_gram' => 0,
                'sales_count' => 0,
                'total_expenses' => 0,
            ];

            if (! $start || ! $end) {
                return $result;
            }

            // Format period label nicely, always start-to-end
            $prettyPeriod = $result['period'];
            try {
                if ($periodType === 'monthly') {
                    // $start and $end are in Y-m format, always use as selected
                    $startObj = \Carbon\Carbon::createFromFormat('Y-m', $start)->startOfMonth();
                    $endObj = \Carbon\Carbon::createFromFormat('Y-m', $end)->endOfMonth();
                    $prettyPeriod = $startObj->format('d-m-Y').' - '.$endObj->format('d-m-Y');
                } elseif ($periodType === 'annual') {
                    $startObj = \Carbon\Carbon::parse($start);
                    $endObj = \Carbon\Carbon::parse($end);
                    if ($startObj->gt($endObj)) {
                        [$startObj, $endObj] = [$endObj, $startObj];
                    }
                    $prettyPeriod = $startObj->format('Y').' - '.$endObj->format('Y');
                } elseif ($periodType === 'weekly' || $periodType === 'special') {
                    $startObj = \Carbon\Carbon::parse($start);
                    $endObj = \Carbon\Carbon::parse($end);
                    if ($startObj->gt($endObj)) {
                        [$startObj, $endObj] = [$endObj, $startObj];
                    }
                    $prettyPeriod = $startObj->format('d-m-Y').' - '.$endObj->format('d-m-Y');
                }
            } catch (\Exception $e) {
            }

            $salesQuery = Sale::notReturned()->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59']);
            if ($branchId) {
                $salesQuery->where('branch_id', $branchId);
            }
            $sales = $salesQuery->get();

            $totalSales = 0;
            $totalWeight = 0;
            $salesCount = 0;
            foreach ($sales as $sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                if ($products) {
                    foreach ($products as $product) {
                        $totalSales += $product['amount'] ?? 0;
                        $totalWeight += $product['weight'] ?? 0;
                        $salesCount++;
                    }
                }
            }

            $expensesQuery = Expense::whereBetween('expense_date', [$start, $end]);
            if ($branchId) {
                $expensesQuery->where('branch_id', $branchId);
            }
            $totalExpenses = $expensesQuery->sum('amount');

            $pricePerGram = $totalWeight > 0 ? ($totalSales / $totalWeight) : 0;

            return [
                'period' => $prettyPeriod,
                'total_sales' => $totalSales,
                'total_weight' => $totalWeight,
                'price_per_gram' => $pricePerGram,
                'sales_count' => $salesCount,
                'total_expenses' => $totalExpenses,
            ];
        };

        $period1 = $computeTotals(
            $from1_date ? $from1_date->toDateString() : null,
            $to1_date ? $to1_date->toDateString() : null,
            $branchId
        );
        $period2 = $computeTotals(
            $from2_date ? $from2_date->toDateString() : null,
            $to2_date ? $to2_date->toDateString() : null,
            $branchId
        );

        // --- New: Aggregation for employees, calibers, categories ---
        $groupAggregates = [];
        $groupTypes = [
            'employees' => ['model' => \App\Models\Employee::class, 'field' => 'employee_id', 'name' => 'name'],
            'calibers' => ['model' => \App\Models\Caliber::class, 'field' => 'caliber_id', 'name' => 'name'],
            'categories' => ['model' => \App\Models\Category::class, 'field' => 'category_id', 'name' => 'name'],
        ];
        foreach ($groupTypes as $type => $info) {
            $all = $info['model']::all();
            $data = [];
            // Always use full day for end date
            $from1_str = $from1_date ? (is_object($from1_date) ? $from1_date->format('Y-m-d') : (string)$from1_date) : null;
            $to1_str = $to1_date ? (is_object($to1_date) ? $to1_date->format('Y-m-d') : (string)$to1_date) : null;
            $from2_str = $from2_date ? (is_object($from2_date) ? $from2_date->format('Y-m-d') : (string)$from2_date) : null;
            $to2_str = $to2_date ? (is_object($to2_date) ? $to2_date->format('Y-m-d') : (string)$to2_date) : null;
            foreach ($all as $item) {
                $id = $item->id;
                $name = $item->{$info['name']};
                $totalSales1 = 0;
                $totalWeight1 = 0;
                $totalSales2 = 0;
                $totalWeight2 = 0;
                if ($type === 'employees') {
                    // Filter by employee_id in SQL
                    $sales1 = \App\Models\Sale::notReturned()
                        ->where('employee_id', $id)
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->whereBetween('created_at', [$from1_str . ' 00:00:00', $to1_str . ' 23:59:59'])
                        ->get();
                    foreach ($sales1 as $sale) {
                        $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                        if ($products) {
                            foreach ($products as $product) {
                                $totalSales1 += $product['amount'] ?? 0;
                                $totalWeight1 += $product['weight'] ?? 0;
                            }
                        }
                    }
                    $sales2 = \App\Models\Sale::notReturned()
                        ->where('employee_id', $id)
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->whereBetween('created_at', [$from2_str . ' 00:00:00', $to2_str . ' 23:59:59'])
                        ->get();
                    foreach ($sales2 as $sale) {
                        $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                        if ($products) {
                            foreach ($products as $product) {
                                $totalSales2 += $product['amount'] ?? 0;
                                $totalWeight2 += $product['weight'] ?? 0;
                            }
                        }
                    }
                } else {
                    // For calibers/categories, get all sales in range and filter products in PHP
                    $sales1 = \App\Models\Sale::notReturned()
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->whereBetween('created_at', [$from1_str . ' 00:00:00', $to1_str . ' 23:59:59'])
                        ->get();
                    foreach ($sales1 as $sale) {
                        $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                        if ($products) {
                            foreach ($products as $product) {
                                if (
                                    ($type === 'calibers' && isset($product['caliber_id']) && $product['caliber_id'] == $id) ||
                                    ($type === 'categories' && isset($product['category_id']) && $product['category_id'] == $id)
                                ) {
                                    $totalSales1 += $product['amount'] ?? 0;
                                    $totalWeight1 += $product['weight'] ?? 0;
                                }
                            }
                        }
                    }
                    $sales2 = \App\Models\Sale::notReturned()
                        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                        ->whereBetween('created_at', [$from2_str . ' 00:00:00', $to2_str . ' 23:59:59'])
                        ->get();
                    foreach ($sales2 as $sale) {
                        $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                        if ($products) {
                            foreach ($products as $product) {
                                if (
                                    ($type === 'calibers' && isset($product['caliber_id']) && $product['caliber_id'] == $id) ||
                                    ($type === 'categories' && isset($product['category_id']) && $product['category_id'] == $id)
                                ) {
                                    $totalSales2 += $product['amount'] ?? 0;
                                    $totalWeight2 += $product['weight'] ?? 0;
                                }
                            }
                        }
                    }
                }
                // Difference %
                $salesDiff = $totalSales2 - $totalSales1;
                $weightDiff = $totalWeight2 - $totalWeight1;
                
                // Calculate percentage even when period 1 is zero
                if ($totalSales1 != 0) {
                    $salesDiffPct = round(($salesDiff / $totalSales1) * 100, 2);
                } elseif ($totalSales2 != 0) {
                    // If period 1 is zero but period 2 has sales, show as 100% increase
                    $salesDiffPct = 100.00;
                } else {
                    $salesDiffPct = 0;
                }
                
                if ($totalWeight1 != 0) {
                    $weightDiffPct = round(($weightDiff / $totalWeight1) * 100, 2);
                } elseif ($totalWeight2 != 0) {
                    // If period 1 is zero but period 2 has weight, show as 100% increase
                    $weightDiffPct = 100.00;
                } else {
                    $weightDiffPct = 0;
                }
                
                // Only add if there is sales in either period
                if ($totalSales1 != 0 || $totalSales2 != 0) {
                    $data[] = [
                        'name' => $name,
                        'total_sales_1' => $totalSales1,
                        'total_sales_2' => $totalSales2,
                        'total_weight_1' => $totalWeight1,
                        'total_weight_2' => $totalWeight2,
                        'sales_diff_pct' => $salesDiffPct,
                        'weight_diff_pct' => $weightDiffPct,
                    ];
                }
            }
            $groupAggregates[$type] = $data;
        }

        $filters = [
            'from1' => $from1,
            'to1' => $to1,
            'from2' => $from2,
            'to2' => $to2,
            'branch_id' => $branchId,
            'period_type' => $periodType,
        ];

        return view('reports.period_comparison', compact('branches', 'filters', 'period1', 'period2', 'groupAggregates'));
    }

    /**
     * Generate report by branch (sales and expenses grouped by branch).
     */
    public function byBranch(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $format = $request->get('format');
        $perPage = (int) $request->get('per_page', 25);

        // Get all branches (active)
        $branches = Branch::active()->get();

        // For each branch, get sales and expenses summary in the filter range
        $branchData = $branches->map(function ($branch) use ($filters) {
            $salesQuery = Sale::notReturned()->where('branch_id', $branch->id);
            $expensesQuery = Expense::where('branch_id', $branch->id);

            // Apply date filters
            if (isset($filters['date_from'])) {
                $salesQuery->whereDate('created_at', '>=', $filters['date_from']);
                $expensesQuery->whereDate('expense_date', '>=', $filters['date_from']);
            }
            if (isset($filters['date_to'])) {
                $salesQuery->whereDate('created_at', '<=', $filters['date_to']);
                $expensesQuery->whereDate('expense_date', '<=', $filters['date_to']);
            }
            $sales = $salesQuery->get();
            $totalSales = 0;
            $totalNetSales = 0;
            $totalWeight = 0;
            $salesCount = 0;
            foreach ($sales as $sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                if ($products) {
                    foreach ($products as $product) {
                        $totalSales += $product['amount'] ?? 0;
                        $totalNetSales += $product['net_amount'] ?? 0;
                        $totalWeight += $product['weight'] ?? 0;
                        $salesCount++;
                    }
                }
            }
            $totalExpenses = $expensesQuery->sum('amount');
            $expensesCount = $expensesQuery->count();
            $netProfit = $totalNetSales - $totalExpenses;
            $avgPricePerGram = $totalWeight > 0 ? $totalSales / $totalWeight : 0;

            // Returned sales for this branch and date range (filtered by returned_at)
            $returnedSales = \App\Models\Sale::where('branch_id', $branch->id)
                ->where('is_returned', true)
                ->whereDate('returned_at', '>=', $filters['date_from'])
                ->whereDate('returned_at', '<=', $filters['date_to'])
                ->get();

            return [
                'branch' => $branch,
                'total_sales' => $totalSales,
                'total_net_sales' => $totalNetSales,
                'total_weight' => $totalWeight,
                'sales_count' => $salesCount,
                'total_expenses' => $totalExpenses,
                'expenses_count' => $expensesCount,
                'net_profit' => $netProfit,
                'avg_price_per_gram' => $avgPricePerGram,
                // Optionally, you can add 'returned_sales' => $returnedSales if needed in the view
            ];
        });

        // You may want to return or use $branchData here, depending on your needs.
        // For now, let's just return a view as an example:
        // return view('reports.by_branch', compact('branchData', 'filters', 'lists'));

    }

    /**
     * Main all-in-one report page (sales, expenses, grouped data, etc.)
     */
    public function all(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $perPage = (int) $request->get('per_page', 10);

        // Sales and Expenses queries with pagination
        $salesQuery = $this->buildSalesQuery($filters)->with(['branch', 'employee', 'caliber']);
        $expensesQuery = $this->buildExpensesQuery($filters)->with(['branch', 'expenseType']);

        $sales = $salesQuery->paginate($perPage, ['*'], 'sales_page');
        $expenses = $expensesQuery->paginate($perPage, ['*'], 'expenses_page');

        // Branch Data - filter by specific branch if selected
        $branchesCollection = isset($filters['branch_id'])
            ? $lists['branches']->where('id', $filters['branch_id'])
            : $lists['branches'];
        $branchData = $branchesCollection->map(function ($branch) use ($filters) {
            $salesQuery = Sale::notReturned()->where('branch_id', $branch->id);
            $expensesQuery = Expense::where('branch_id', $branch->id);
            if (isset($filters['date_from'])) {
                $salesQuery->whereDate('created_at', '>=', $filters['date_from']);
                $expensesQuery->whereDate('expense_date', '>=', $filters['date_from']);
            }
            if (isset($filters['date_to'])) {
                $salesQuery->whereDate('created_at', '<=', $filters['date_to']);
                $expensesQuery->whereDate('expense_date', '<=', $filters['date_to']);
            }
            $totalSales = $salesQuery->sum('total_amount');
            $totalNetSales = $salesQuery->sum('net_amount');
            $sales = $salesQuery->get();
            $totalWeight = 0;
            foreach ($sales as $sale) {
                if (is_array($sale->products)) {
                    foreach ($sale->products as $product) {
                        $totalWeight += isset($product['weight']) ? (float) $product['weight'] : 0;
                    }
                }
            }
            $salesCount = $salesQuery->count();
            $totalExpenses = $expensesQuery->sum('amount');
            $expensesCount = $expensesQuery->count();
            $netProfit = $totalNetSales - $totalExpenses;

            return [
                'branch' => $branch,
                'total_sales' => $totalSales,
                'total_net_sales' => $totalNetSales,
                'total_weight' => $totalWeight,
                'sales_count' => $salesCount,
                'total_expenses' => $totalExpenses,
                'expenses_count' => $expensesCount,
                'net_profit' => $netProfit,
            ];
        })->sortByDesc('total_sales')->values();

        // Employees Data - filter by specific employee if selected, or by branch if branch is selected
        $employeesCollection = $lists['employees'];
        if (isset($filters['employee_id'])) {
            $employeesCollection = $employeesCollection->where('id', $filters['employee_id']);
        } elseif (isset($filters['branch_id'])) {
            $employeesCollection = $employeesCollection->where('branch_id', $filters['branch_id']);
        }
        $employeesData = $employeesCollection->map(function ($employee) use ($filters) {
            $salesQuery = Sale::notReturned()->where('employee_id', $employee->id);
            if (isset($filters['branch_id'])) {
                $salesQuery->where('branch_id', $filters['branch_id']);
            }
            if (isset($filters['date_from'])) {
                $salesQuery->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (isset($filters['date_to'])) {
                $salesQuery->whereDate('created_at', '<=', $filters['date_to']);
            }
            $sales = $salesQuery->get();
            $netAmount = $salesQuery->sum('net_amount');

            return [
                'employee' => $employee,
                'total_sales' => $salesQuery->sum('total_amount'),
                'total_weight' => $sales->reduce(function ($carry, $sale) {
                    if (is_array($sale->products)) {
                        foreach ($sale->products as $product) {
                            $carry += isset($product['weight']) ? (float) $product['weight'] : 0;
                        }
                    }

                    return $carry;
                }, 0),
                'sales_count' => $salesQuery->count(),
                'net_profit' => $netAmount,
            ];
        })->sortByDesc('total_sales')->values();

        // Refactored summary to use products array for weight/caliber
        $totalWeight = 0;
        $totalSales = 0;
        $totalNetSales = 0;
        $totalTax = 0;
        foreach ($sales as $sale) {
            $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
            if ($products) {
                foreach ($products as $product) {
                    $totalWeight += $product['weight'] ?? 0;
                    $totalSales += $product['amount'] ?? 0;
                    $totalNetSales += $product['net_amount'] ?? 0;
                    $totalTax += $product['tax_amount'] ?? 0;
                }
            }
        }
        $summary = [
            'total_sales' => $totalNetSales, // مطابق لصافي المبيعات
            'total_net_sales' => $totalNetSales,
            'total_tax' => $totalTax,
            'total_weight' => $totalWeight,
            'total_expenses' => $expenses->sum('amount'),
            'net_profit' => $totalNetSales - $expenses->sum('amount'),
            'sales_count' => $sales->count(),
            'expenses_count' => $expenses->count(),
        ];

        // Refactored groupedData to use products array for weight/caliber
        $groupedData = [
            'by_branch' => $sales->groupBy('branch.name')->map(function ($group) {
                $weight = 0;
                foreach ($group as $sale) {
                    $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                    if ($products) {
                        foreach ($products as $product) {
                            $weight += $product['weight'] ?? 0;
                        }
                    }
                }

                return ['sales' => $group, 'weight' => $weight];
            }),
            'by_employee' => $sales->groupBy('employee.name')->map(function ($group) {
                $weight = 0;
                foreach ($group as $sale) {
                    $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                    if ($products) {
                        foreach ($products as $product) {
                            $weight += $product['weight'] ?? 0;
                        }
                    }
                }

                return ['sales' => $group, 'weight' => $weight];
            }),
            'by_category' => $sales->flatMap(function ($sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                $result = collect();
                if ($products) {
                    foreach ($products as $product) {
                        $result->push(['category_id' => $product['category_id'] ?? null, 'weight' => $product['weight'] ?? 0]);
                    }
                }

                return $result;
            })->groupBy('category_id'),
            'by_caliber' => $sales->flatMap(function ($sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                $result = collect();
                if ($products) {
                    foreach ($products as $product) {
                        $result->push(['caliber_id' => $product['caliber_id'] ?? null, 'weight' => $product['weight'] ?? 0]);
                    }
                }

                return $result;
            })->groupBy('caliber_id'),
            'by_date' => $sales->groupBy(function ($sale) {
                return $sale->created_at->format('Y-m-d');
            }),
        ];

        // Pass all lists for filters
        $branches = $lists['branches'];
        $employees = $lists['employees'];
        $categories = $lists['categories'];
        $calibers = $lists['calibers'];
        $expenseTypes = $lists['expenseTypes'];

        // Load minimum price setting from database
        $minGramPrice = (float) \App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 2.0));

        $data = compact('summary', 'sales', 'expenses', 'groupedData', 'branchData', 'employeesData', 'branches', 'employees', 'categories', 'calibers', 'expenseTypes', 'minGramPrice');

        if ($request->get('format') === 'pdf') {
            // Remove logo and company info from $data for PDF
            $data['settings'] = [
                'company_name' => '',
                'address' => '',
                'phones' => '',
                'tax_number' => '',
                'commercial_register' => '',
                'logo_path' => '',
            ];

            return $this->generatePDF('reports.all', $data, 'جميع_التقارير');
        }
        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'all_reports.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'all_reports.csv', ExcelFormat::CSV);
        }

        return view('reports.all', $data);
    }
    // (Removed invalid code block that was outside of any function)

    /**
     * Display speed report with quick metrics.
     */
    public function speed(Request $request)
    {
        $this->validateDeviceOrAbort();
        // ...existing code...
        $branchId = $request->get('branch_id');
        $date = $request->get('date', date('Y-m-d'));

        // Quick metrics using raw queries for speed
        $salesQuery = DB::table('sales')
            ->where('is_returned', false)
            ->whereDate('created_at', $date);

        $expensesQuery = DB::table('expenses')
            ->whereDate('expense_date', $date);

        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
            $expensesQuery->where('branch_id', $branchId);
        }

        // Get aggregated data in single queries
        $salesStats = $salesQuery->selectRaw('
            COUNT(*) as count,
            SUM(total_amount) as total,
            SUM(net_amount) as net,
            SUM(tax_amount) as tax,
            SUM(cash_amount) as cash,
            SUM(network_amount) as network
        ')->first();

        $expensesStats = $expensesQuery->selectRaw('
            COUNT(*) as count,
            SUM(amount) as total
        ')->first();

        // Calculate total weight from products JSON
        $salesForWeight = \App\Models\Sale::notReturned()
            ->whereDate('created_at', $date);
        if ($branchId) {
            $salesForWeight->where('branch_id', $branchId);
        }
        $salesForWeight = $salesForWeight->get();
        $totalWeightAllSales = 0;
        foreach ($salesForWeight as $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            if ($products) {
                foreach ($products as $product) {
                    $totalWeightAllSales += isset($product['weight']) ? (float) $product['weight'] : 0;
                }
            }
        }

        // Calculate derived metrics
        $profit = ($salesStats->net ?? 0) - ($expensesStats->total ?? 0);
        $pricePerGram = ($totalWeightAllSales > 0) ? (($salesStats->total ?? 0) / $totalWeightAllSales) : 0;

        // Top 5 employees (fast query)
        $topEmployeesRaw = DB::table('sales')
            ->join('employees', 'sales.employee_id', '=', 'employees.id')
            ->where('sales.is_returned', false)
            ->whereDate('sales.created_at', $date)
            ->when($branchId, fn ($q) => $q->where('sales.branch_id', $branchId))
            ->selectRaw('employees.id, employees.name, COUNT(*) as sales_count, SUM(sales.total_amount) as total_sales')
            ->groupBy('employees.id', 'employees.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Calculate total_weight for each employee from products array
        $topEmployees = collect();
        foreach ($topEmployeesRaw as $emp) {
            $sales = \App\Models\Sale::notReturned()
                ->whereDate('created_at', $date)
                ->where('employee_id', $emp->id);
            if ($branchId) {
                $sales->where('branch_id', $branchId);
            }
            $sales = $sales->get();
            $empWeight = 0;
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $empWeight += isset($product['weight']) ? (float) $product['weight'] : 0;
                    }
                }
            }
            $topEmployees->push((object) [
                'name' => $emp->name,
                'sales_count' => $emp->sales_count,
                'total_sales' => $emp->total_sales,
                'total_weight' => $empWeight,
            ]);
        }

        // Refactored: Sales by caliber using products array
        $salesQuery = \App\Models\Sale::notReturned()
            ->whereDate('created_at', $date);
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }
        $sales = $salesQuery->get();

        // Dynamically collect all caliber_ids used in today's sales
        $usedCaliberIds = collect();
        foreach ($sales as $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            if ($products) {
                foreach ($products as $product) {
                    if (isset($product['caliber_id'])) {
                        $usedCaliberIds->push($product['caliber_id']);
                    }
                }
            }
        }
        $usedCaliberIds = $usedCaliberIds->unique()->filter()->values();
        $calibers = \App\Models\Caliber::whereIn('id', $usedCaliberIds)->get();

        $salesByCaliber = collect();
        foreach ($calibers as $caliber) {
            $count = 0;
            $amount = 0;
            $weight = 0;
            $salesWithoutTax = 0;
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    // Calculate total amount for proportional distribution
                    $totalProductAmount = 0;
                    foreach ($products as $product) {
                        $totalProductAmount += isset($product['amount']) ? (float) $product['amount'] : 0;
                    }
                    if ($totalProductAmount == 0) {
                        $totalProductAmount = 1;
                    } // avoid division by zero
                    foreach ($products as $product) {
                        if (isset($product['caliber_id']) && $product['caliber_id'] == $caliber->id) {
                            $count++;
                            $weight += isset($product['weight']) ? (float) $product['weight'] : 0;
                            // Distribute sale total_amount proportionally to product amount
                            $share = isset($product['amount']) ? (float) $product['amount'] / $totalProductAmount : 0;
                            $amount += isset($sale->total_amount) ? $sale->total_amount * $share : 0;
                            $salesWithoutTax += isset($sale->net_amount) ? $sale->net_amount * $share : 0;
                        }
                    }
                }
            }
            $salesByCaliber->push((object) [
                'name' => $caliber->name,
                'count' => $count,
                'amount' => $amount,
                'weight' => $weight,
                'sales_without_tax' => $salesWithoutTax,
            ]);
        }

        // Payment methods breakdown
        $paymentMethods = DB::table('sales')
            ->where('is_returned', false)
            ->whereDate('created_at', $date)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as amount')
            ->whereIn('payment_method', ['cash', 'network', 'mixed', 'transfer', 'snap'])
            ->groupBy('payment_method')
            ->get();

        // Top expense types
        $topExpenseTypes = DB::table('expenses')
            ->join('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->whereDate('expenses.expense_date', $date)
            ->when($branchId, fn ($q) => $q->where('expenses.branch_id', $branchId))
            ->selectRaw('expense_types.name, COUNT(*) as count, SUM(expenses.amount) as total')
            ->groupBy('expense_types.id', 'expense_types.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $branches = Branch::active()->get();

        $metrics = [
            'sales_count' => $salesStats->count ?? 0,
            'sales_total' => $salesStats->total ?? 0,
            'sales_net' => $salesStats->net ?? 0,
            'sales_tax' => $salesStats->tax ?? 0,
            'sales_weight' => $totalWeightAllSales,
            'cash_amount' => $salesStats->cash ?? 0,
            'network_amount' => $salesStats->network ?? 0,
            'expenses_count' => $expensesStats->count ?? 0,
            'expenses_total' => $expensesStats->total ?? 0,
            'profit' => $profit,
            'price_per_gram' => $pricePerGram,
        ];

        $data = compact(
            'metrics',
            'topEmployees',
            'salesByCaliber',
            'paymentMethods',
            'topExpenseTypes',
            'branches',
            'branchId',
            'date'
        );

        // Handle exports
        $format = $request->get('format');
        if ($format === 'pdf') {
            return $this->generatePDF('reports.speed', $data, 'تقرير_سريع_'.$date);
        }
        if ($format === 'csv') {
            $filename = 'speed_report_'.$date.'.csv';

            return response()->streamDownload(function () use ($metrics, $topEmployees, $salesByCaliber, $date, $branchId, $branches) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM

                // Header info
                fputcsv($out, ['التقرير السريع']);
                fputcsv($out, ['التاريخ', $date]);
                fputcsv($out, ['الفرع', $branchId ? $branches->find($branchId)?->name : 'جميع الفروع']);
                fputcsv($out, []);

                // Metrics
                fputcsv($out, ['المقاييس الرئيسية']);
                fputcsv($out, ['المؤشر', 'القيمة']);
                fputcsv($out, ['إجمالي المبيعات', $metrics['sales_total']]);
                fputcsv($out, ['عدد الفواتير', $metrics['sales_count']]);
                fputcsv($out, ['إجمالي المصروفات', $metrics['expenses_total']]);
                fputcsv($out, ['إجمالي الوزن', $metrics['sales_weight']]);
                fputcsv($out, ['متوسط سعر الجرام', $metrics['price_per_gram']]);
                fputcsv($out, []);

                // Top employees
                fputcsv($out, ['أفضل 5 موظفين']);
                fputcsv($out, ['الموظف', 'العدد', 'المبيعات', 'الوزن']);
                foreach ($topEmployees as $emp) {
                    fputcsv($out, [$emp->name, $emp->sales_count, $emp->total_sales, $emp->total_weight]);
                }
                fputcsv($out, []);

                // Sales by caliber
                fputcsv($out, ['المبيعات حسب العيار']);
                fputcsv($out, ['العيار', 'العدد', 'المبلغ', 'الوزن']);
                foreach ($salesByCaliber as $caliber) {
                    fputcsv($out, [$caliber->name, $caliber->count, $caliber->amount, $caliber->weight]);
                }

                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return view('reports.speed', $data);
    }

    /**
        $pricePerGram = ($totalWeight > 0) ? ($salesStats->total ?? 0) / $totalWeight : 0;
     */
    public function index()
    {
        $this->validateDeviceOrAbort();
        $branches = Branch::active()->get();
        $employees = Employee::active()->get();
        $categories = Category::active()->get();
        $calibers = Caliber::active()->get();
        $expenseTypes = ExpenseType::active()->get();

        return view('reports.index', compact(
            'branches',
            'employees',
            'categories',
            'calibers',
            'expenseTypes'
        ));
    }

    /**
     * Generate comprehensive report.
     */
    public function comprehensive(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $format = $request->get('format');
        $perPage = (int) $request->get('per_page', 25);

        // Base queries
        $salesQuery = $this->buildSalesQuery($filters)->with(['branch', 'employee', 'category', 'caliber']);
        $expensesQuery = $this->buildExpensesQuery($filters)->with(['branch', 'expenseType']);

        // For HTML view: paginate; For exports/print: fetch all
        if ($format) {
            $sales = (clone $salesQuery)->get();
            $expenses = (clone $expensesQuery)->get();
        } else {
            $sales = (clone $salesQuery)->paginate($perPage)->appends($request->query());
            $expenses = (clone $expensesQuery)->paginate($perPage)->appends($request->query());
        }

        // Calculate summaries
        $summary = [
            'total_sales' => (clone $salesQuery)->sum('net_amount'), // مطابق لصافي المبيعات
            'total_net_sales' => (clone $salesQuery)->sum('net_amount'),
            'total_tax' => (clone $salesQuery)->sum('tax_amount'),
            // ...existing code...
            'total_expenses' => (clone $expensesQuery)->sum('amount'),
            'net_profit' => (clone $salesQuery)->sum('net_amount') - (clone $expensesQuery)->sum('amount'),
            'sales_count' => (clone $salesQuery)->count(),
            'expenses_count' => (clone $expensesQuery)->count(),
        ];

        $data = compact('sales', 'expenses', 'summary', 'filters') + $lists;

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.comprehensive', $data, 'تقرير شامل');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'comprehensive_report.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'comprehensive_report.csv', ExcelFormat::CSV);
        }

        return view('reports.comprehensive', $data);
    }

    /**
     * Generate detailed sales report.
     */
    public function detailed(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();

        $salesQuery = $this->buildSalesQuery($filters);
        $sales = $salesQuery->with(['branch', 'employee', 'caliber'])->get();

        // Group by different criteria
        $groupedData = [
            'by_branch' => $sales->groupBy('branch.name'),
            'by_employee' => $sales->groupBy('employee.name'),
            'by_category' => $sales->groupBy('category.name'),
            'by_caliber' => $sales->groupBy('caliber.name'),
            'by_date' => $sales->groupBy(function ($sale) {
                return $sale->created_at->format('Y-m-d');
            }),
        ];

        $summary = [
            'total_sales' => $sales->sum('total_amount'),
            'total_net_sales' => $sales->sum('net_amount'),
            // ...existing code...
            'sales_count' => $sales->count(),
        ];

        $data = compact('sales', 'groupedData', 'summary', 'filters') + $lists;

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.detailed', $data, 'تقرير مفصل');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'detailed_report.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'detailed_report.csv', ExcelFormat::CSV);
        }

        return view('reports.detailed', $data);
    }

    /**
     * Generate calibers report.
     */
    public function calibers(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $format = $request->get('format');
        $perPage = (int) $request->get('per_page', 25);

        $calibersQuery = Caliber::active()
            ->withCount(['sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }])
            ->with(['sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }]);

        if ($format) {
            $calibersData = $calibersQuery->get()->map(function ($caliber) {
                return [
                    'caliber' => $caliber,
                    'total_amount' => $caliber->sales->sum('total_amount'),
                    // ...existing code...
                    'total_tax' => $caliber->sales->sum('tax_amount'),
                    'net_amount' => $caliber->sales->sum('net_amount'),
                    'sales_count' => $caliber->sales_count,
                ];
            });
        } else {
            $calibersData = $calibersQuery
                ->paginate($perPage)
                ->through(function ($caliber) {
                    return [
                        'caliber' => $caliber,
                        'total_amount' => $caliber->sales->sum('total_amount'),
                        'total_weight' => $caliber->sales->reduce(function ($carry, $sale) {
                            if (is_array($sale->products)) {
                                foreach ($sale->products as $product) {
                                    $carry += isset($product['weight']) ? (float) $product['weight'] : 0;
                                }
                            }

                            return $carry;
                        }, 0),
                        'total_tax' => $caliber->sales->sum('tax_amount'),
                        'net_amount' => $caliber->sales->sum('net_amount'),
                        'sales_count' => $caliber->sales_count,
                    ];
                })
                ->appends($request->query());
        }

        $data = compact('calibersData', 'filters') + $lists;

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.calibers', $data, 'تقرير العيارات');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'calibers_report.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'calibers_report.csv', ExcelFormat::CSV);
        }

        return view('reports.calibers', $data);
    }

    /**
     * Generate categories report.
     */
    public function categories(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $format = $request->get('format');
        $perPage = (int) $request->get('per_page', 25);

        $categories = Category::active()->orderBy('name')->get();
        $sales = $this->buildSalesQuery($filters)->where('is_returned', false)->get();

        $categoriesData = $categories->map(function ($category) use ($sales) {
            $totalAmount = 0;
            $totalWeight = 0;
            $salesCount = 0;
            foreach ($sales as $sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                if ($products) {
                    foreach ($products as $product) {
                        if (($product['category_id'] ?? null) == $category->id) {
                            $totalAmount += $product['amount'] ?? 0;
                            $totalWeight += $product['weight'] ?? 0;
                            $salesCount++;
                        }
                    }
                }
            }

            return [
                'category' => $category,
                'total_amount' => $totalAmount,
                'total_weight' => $totalWeight,
                'sales_count' => $salesCount,
            ];
        });

        $data = compact('categoriesData', 'filters') + $lists;

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.categories', $data, 'تقرير الأصناف');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'categories_report.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'categories_report.csv', ExcelFormat::CSV);
        }

        return view('reports.categories', $data);
    }

    /**
     * Generate employees report.
     */
    public function employees(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $format = $request->get('format');
        $perPage = (int) $request->get('per_page', 25);

        $employeesQuery = Employee::active()
            ->with(['branch', 'sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }]);

        if ($format) {
            $employeesData = $employeesQuery->get()->map(function ($employee) {
                return [
                    'employee' => $employee,
                    'total_sales' => $employee->sales->sum('total_amount'),
                    'total_weight' => $employee->sales->reduce(function ($carry, $sale) {
                        if (is_array($sale->products)) {
                            foreach ($sale->products as $product) {
                                $carry += isset($product['weight']) ? (float) $product['weight'] : 0;
                            }
                        }

                        return $carry;
                    }, 0),
                    'sales_count' => $employee->sales->count(),
                    'net_profit' => $employee->sales->sum('net_amount') - $employee->salary,
                ];
            });
        } else {
            $employeesData = $employeesQuery
                ->paginate($perPage)
                ->through(function ($employee) {
                    return [
                        'employee' => $employee,
                        'total_sales' => $employee->sales->sum('total_amount'),
                        'total_weight' => $employee->sales->sum('weight'),
                        'sales_count' => $employee->sales->count(),
                        'net_profit' => $employee->sales->sum('net_amount') - $employee->salary,
                    ];
                })
                ->appends($request->query());
        }

        // Load minimum price setting from database
        $minGramPrice = (float) \App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 2.0));

        $data = compact('employeesData', 'filters', 'minGramPrice') + $lists;

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.employees', $data, 'تقرير الموظفين');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'employees_report.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'employees_report.csv', ExcelFormat::CSV);
        }

        return view('reports.employees', $data);
    }

    /**
     * Generate net profit report after deducting wages and salaries.
     */
    public function netProfit(Request $request)
    {
        $this->validateDeviceOrAbort();
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();

        // Get sales data
        $salesQuery = $this->buildSalesQuery($filters);
        $totalSales = $salesQuery->sum('net_amount');
        $totalWeight = $salesQuery->sum('weight');

        // Get expenses data
        $expensesQuery = $this->buildExpensesQuery($filters);
        $totalExpenses = $expensesQuery->sum('amount');

        // Calculate employee salaries for the period
        $employeesQuery = Employee::active();
        if (isset($filters['branch_id'])) {
            $employeesQuery->where('branch_id', $filters['branch_id']);
        }
        $totalSalaries = $employeesQuery->sum('salary');

        // Net profit calculation
        $netProfit = $totalSales - $totalExpenses - $totalSalaries;

        $data = [
            'total_sales' => $totalSales,
            'total_weight' => $totalWeight,
            'total_expenses' => $totalExpenses,
            'total_salaries' => $totalSalaries,
            'net_profit' => $netProfit,
            'filters' => $filters,
        ] + $lists;

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.net_profit', $data, 'تقرير صافي الربح');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'net_profit_report.xlsx');
        }
        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportsExport($data), 'net_profit_report.csv', ExcelFormat::CSV);
        }

        return view('reports.net_profit', $data);
    }

    /**
     * Build sales query with filters.
     */
    private function buildSalesQuery($filters)
    {
        $query = Sale::notReturned();

        return $this->applySalesFilters($query, $filters)->orderBy('total_amount', 'desc');
    }

    /**
     * Apply sales filters to query.
     */
    private function applySalesFilters($query, $filters)
    {
        if (isset($filters['sale_id'])) {
            $query->where('id', $filters['sale_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        // Note: category_id filter removed - categories are now in products JSON
        // if (isset($filters['category_id'])) {
        //     $query->where('category_id', $filters['category_id']);
        // }

        if (isset($filters['caliber_id'])) {
            $query->where('caliber_id', $filters['caliber_id']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Build expenses query with filters.
     */
    private function buildExpensesQuery($filters)
    {
        $query = Expense::query();

        if (isset($filters['expense_id'])) {
            $query->where('id', $filters['expense_id']);
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['expense_type_id'])) {
            $query->where('expense_type_id', $filters['expense_type_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Validate and prepare filters.
     */
    private function validateFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('sale_id')) {
            $filters['sale_id'] = $request->get('sale_id');
        }

        if ($request->filled('expense_id')) {
            $filters['expense_id'] = $request->get('expense_id');
        }

        if ($request->filled('branch') || $request->filled('branch_id')) {
            $filters['branch_id'] = $request->get('branch') ?: $request->get('branch_id');
        }

        if ($request->filled('employee') || $request->filled('employee_id')) {
            $filters['employee_id'] = $request->get('employee') ?: $request->get('employee_id');
        }

        // Note: category_id filter removed - categories are now in products JSON
        // if ($request->filled('category') || $request->filled('category_id')) {
        //     $filters['category_id'] = $request->get('category') ?: $request->get('category_id');
        // }

        if ($request->filled('caliber') || $request->filled('caliber_id')) {
            $filters['caliber_id'] = $request->get('caliber') ?: $request->get('caliber_id');
        }

        if ($request->filled('expense_type') || $request->filled('expense_type_id')) {
            $filters['expense_type_id'] = $request->get('expense_type') ?: $request->get('expense_type_id');
        }

        if ($request->filled('payment_method')) {
            $filters['payment_method'] = $request->payment_method;
        }

        $filters['date_from'] = $request->get('from') ?: $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $filters['date_to'] = $request->get('to') ?: $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        return $filters;
    }

    /**
     * Get dropdown lists for report filters.
     */
    private function getFilterLists()
    {
        return [
            'branches' => Branch::active()->get(),
            'employees' => Employee::active()->get(),
            'categories' => Category::active()->get(),
            'calibers' => Caliber::active()->get(),
            'expenseTypes' => ExpenseType::active()->get(),
        ];
    }

    /**
     * Kasr Report - تقرير الكسر (Input Form)
     */
    public function kasr(Request $request)
    {
        $this->validateDeviceOrAbort();
        // Calculate total tax for returns
        $totalReturnTax = 0;
        if ($request->branch_id && $request->date_from && $request->date_to) {
            $returnedSales = \App\Models\Sale::where('branch_id', $request->branch_id)
                ->where('is_returned', true)
                ->whereBetween('returned_at', [$request->date_from, $request->date_to])
                ->get();
            foreach ($returnedSales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $totalReturnTax += isset($product['tax_amount']) ? (float) $product['tax_amount'] : 0;
                    }
                }
            }
        }

        // Calculate total returns (sum of net_amount for returned sales)
        $totalReturns = 0;
        // ...existing code...
        $branches = Branch::active()->get();
        $filters = [
            'branch_id' => $request->get('branch_id'),
            'date_from' => $request->get('date_from', date('Y-m-01')),
            'date_to' => $request->get('date_to', date('Y-m-d')),
            'auto_refresh' => $request->get('auto_refresh') === '1',
            'interest_rate' => (float) $request->get('interest_rate', 0),
        ];
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $returnedSales = \App\Models\Sale::where('branch_id', $filters['branch_id'])
                ->where('is_returned', true)
                ->whereBetween('returned_at', [$filters['date_from'], $filters['date_to']])
                ->get();
            foreach ($returnedSales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $totalReturns += isset($product['net_amount'])
                            ? (float) $product['net_amount']
                            : ((float) ($product['amount'] ?? 0) - (float) ($product['tax_amount'] ?? 0));
                    }
                }
            }
        }

        $debugExpenseQuery = [];
        $debugSalaryQuery = [];

        $branches = Branch::active()->get();
        $filters = [
            'branch_id' => $request->get('branch_id'),
            'date_from' => $request->get('date_from', date('Y-m-01')),
            'date_to' => $request->get('date_to', date('Y-m-d')),
            'auto_refresh' => $request->get('auto_refresh') === '1',
            'interest_value' => (float) $request->get('interest_rate', 0), // now value not percent
        ];

        // Auto-load expenses (all) and salaries from database
        // Only use user-provided values on POST, otherwise always use calculated values
        $expenses = 0;
        $expensesList = [];
        $salaries = 0;
        $salariesList = [];
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $expensesQuery = Expense::where('branch_id', $filters['branch_id'])
                ->whereDate('expense_date', '>=', $filters['date_from'])
                ->whereDate('expense_date', '<=', $filters['date_to']);
            $debugExpenseQuery = $expensesQuery->get()->toArray();
            $expenses = collect($debugExpenseQuery)->sum('amount');
            // Add current sum
            $expensesList[] = $expenses;
            // Optionally, add previous expense sums (e.g., from previous months)
            $prevMonths = 3;
            for ($i = 1; $i <= $prevMonths; $i++) {
                $prevFrom = date('Y-m-01', strtotime("-{$i} month", strtotime($filters['date_from'])));
                $prevTo = date('Y-m-t', strtotime("-{$i} month", strtotime($filters['date_from'])));
                $prevExpenses = Expense::where('branch_id', $filters['branch_id'])
                    ->whereDate('expense_date', '>=', $prevFrom)
                    ->whereDate('expense_date', '<=', $prevTo)
                    ->sum('amount');
                if (! in_array($prevExpenses, $expensesList)) {
                    $expensesList[] = $prevExpenses;
                }
            }
            // Remove duplicates and sort descending
            $expensesList = array_unique($expensesList);
            rsort($expensesList);
        }
        // Salaries: always sum all employees of the branch, no date filter
        if ($filters['branch_id']) {
            $salariesQuery = Employee::active()->where('branch_id', $filters['branch_id']);
            $debugSalaryQuery = $salariesQuery->get()->toArray();
            $salaries = collect($debugSalaryQuery)->sum('salary');
            $salariesList = [$salaries];
        }
        // Debug logging
        \Log::info('KASR FILTERS', $filters);
        \Log::info('KASR EXPENSES QUERY', $debugExpenseQuery);
        \Log::info('KASR SALARIES QUERY', $debugSalaryQuery);
        \Log::info('KASR EXPENSES SUM', ['sum' => $expenses]);
        \Log::info('KASR SALARIES SUM', ['sum' => $salaries]);
        \Log::info('KASR EXPENSES QUERY', $debugExpenseQuery);
        \Log::info('KASR SALARIES QUERY', $debugSalaryQuery);
        \Log::info('KASR EXPENSES SUM', ['sum' => $expenses]);
        \Log::info('KASR SALARIES SUM', ['sum' => $salaries]);

        if ($request->isMethod('post')) {
            \Log::info('KASR POSTED VALUES', [
                'expenses' => $request->input('expenses'),
                'salaries' => $request->input('salaries'),
            ]);
            if ($request->has('expenses')) {
                $postedExpenses = $request->input('expenses');
                if ($postedExpenses !== null && $postedExpenses !== '') {
                    $expenses = floatval($postedExpenses);
                }
            }
            if ($request->has('salaries')) {
                $postedSalaries = $request->input('salaries');
                if ($postedSalaries !== null && $postedSalaries !== '') {
                    $salaries = floatval($postedSalaries);
                }
            }
        }

        // Get calibers
        $calibers = Caliber::active()->orderBy('id')->get();
        $weights = [];
        // Map caliber names to IDs for flexible matching
        $caliberNameToId = [];
        foreach ($calibers as $caliber) {
            $weights[$caliber->id] = 0;
            $caliberNameToId[trim($caliber->name)] = $caliber->id;
        }
        // Only use sales (not returns) for by-caliber calculations
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $sales = \App\Models\Sale::notReturned()
                ->where('branch_id', $filters['branch_id'])
                ->whereDate('created_at', '>=', $filters['date_from'])
                ->whereDate('created_at', '<=', $filters['date_to'])
                ->get();
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $cid = isset($product['caliber_id']) ? (int) $product['caliber_id'] : null;
                        $cname = isset($product['caliber_name']) ? trim($product['caliber_name']) : null;
                        $targetId = null;
                        if ($cid && isset($weights[$cid])) {
                            $targetId = $cid;
                        } elseif ($cname && isset($caliberNameToId[$cname])) {
                            $targetId = $caliberNameToId[$cname];
                        }
                        if ($targetId && isset($product['weight'])) {
                            $weights[$targetId] += (float) $product['weight'];
                        }
                    }
                }
            }
        }

        $prices = [];
        $postedPrices = [];
        foreach ($calibers as $index => $caliber) {
            $inputName = isset($caliber->id) ? 'price_'.$caliber->id : 'price_'.$index;
            $prices[$caliber->id ?? $index] = (float) $request->get($inputName, 0);
            $postedPrices[$inputName] = $request->get($inputName);
        }
        \Log::info('KASR POSTED CALIBER PRICES', $postedPrices);
        $reportCalibers = [];
        $totalAmount = 0;
        $totalWeight = 0;
        $totalWeightSales = 0;
        $alIjmali = 0; // إجمالي المبيعات (sum of all product amounts, no deductions)
        $totalSales = 0; // for other uses if needed
        $totalNetSales = 0; // net (without tax, no deductions)
        $totalTax = 0;
        $totalWeightReturns = 0;
        $totalTaxReturns = 0;
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $sales = \App\Models\Sale::notReturned()
                ->where('branch_id', $filters['branch_id'])
                ->whereDate('created_at', '>=', $filters['date_from'])
                ->whereDate('created_at', '<=', $filters['date_to'])
                ->get();
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $alIjmali += isset($product['amount']) ? (float) $product['amount'] : 0; // إجمالي المبيعات الحقيقي
                        $totalSales += isset($product['amount']) ? (float) $product['amount'] : 0; // for compatibility
                        $totalNetSales += isset($product['net_amount']) ? (float) $product['net_amount'] : 0;
                        $totalTax += isset($product['tax_amount']) ? (float) $product['tax_amount'] : 0;
                        $totalWeightSales += isset($product['weight']) ? (float) $product['weight'] : 0;
                    }
                }
            }
            // Calculate total weight and net sales for returns
            $returnedSales = \App\Models\Sale::where('branch_id', $filters['branch_id'])
                ->where('is_returned', true)
                ->whereDate('returned_at', '>=', $filters['date_from'])
                ->whereDate('returned_at', '<=', $filters['date_to'])
                ->get();
            $totalReturns = 0;
            foreach ($returnedSales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $totalReturns += isset($product['net_amount']) ? (float) $product['net_amount'] : 0;
                        $totalWeightReturns += isset($product['weight']) ? (float) $product['weight'] : 0;
                        $totalTaxReturns += isset($product['tax_amount']) ? (float) $product['tax_amount'] : 0;
                    }
                }
            }
            // Final total weight = sales - returns
            $totalWeight = $totalWeightSales - $totalWeightReturns;
        }
        // Calculate cash (sum of sales without tax) for each caliber (only sales, not returns)
        $caliberCash = [];
        foreach ($calibers as $caliber) {
            $caliberCash[$caliber->id] = 0;
        }
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $sales = \App\Models\Sale::notReturned()
                ->where('branch_id', $filters['branch_id'])
                ->whereDate('created_at', '>=', $filters['date_from'])
                ->whereDate('created_at', '<=', $filters['date_to'])
                ->get();
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $cid = isset($product['caliber_id']) ? (int) $product['caliber_id'] : null;
                        $cname = isset($product['caliber_name']) ? trim($product['caliber_name']) : null;
                        $targetId = null;
                        if ($cid && isset($caliberCash[$cid])) {
                            $targetId = $cid;
                        } elseif ($cname && isset($caliberNameToId[$cname])) {
                            $targetId = $caliberNameToId[$cname];
                        }
                        if ($targetId) {
                            $cash = (float) ($product['amount'] ?? 0) - (float) ($product['tax_amount'] ?? 0);
                            $caliberCash[$targetId] += $cash;
                        }
                    }
                }
            }
        }
        $totalCalibersCost = 0;
        foreach ($calibers as $caliber) {
            $weight = $weights[$caliber->id];
            $price = $prices[$caliber->id];
            $amount = $weight * $price;
            $cash = $caliberCash[$caliber->id];
            $avg_price_per_gram = ($weight > 0) ? ($cash / $weight) : 0;
            $reportCalibers[] = [
                'id' => $caliber->id,
                'name' => $caliber->name,
                'weight' => $weight,
                'price_per_gram' => $price,
                'amount' => $amount,
                'cash' => $cash,
                'avg_price_per_gram' => $avg_price_per_gram,
            ];
            $totalAmount += $amount;
            $totalWeight += $weight;
            $totalCalibersCost += $weight * $price;
        }
        $interestValue = (float) ($filters['interest_value'] ?? 0);
        $interestAmount = $interestValue;
        // صافي المبيعات: sum net_amount where is_returned = 0
        $netSales = 0;
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $sales = \App\Models\Sale::notReturned()
                ->where('branch_id', $filters['branch_id'])
                ->whereDate('created_at', '>=', $filters['date_from'])
                ->whereDate('created_at', '<=', $filters['date_to'])
                ->get();
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $netSales += isset($product['net_amount']) ? (float) $product['net_amount'] : 0;
                    }
                }
            }
        }
        // Ensure net_sales is sum of all sales (net_amount) minus sum of all returns (net_amount)
        // totalSales and totalReturns are already calculated as such above
        $netTax = $totalTax; // Net tax: sales taxes minus returns taxes
        $totalWeightAll = $totalWeight + $totalWeightReturns;
        // معدل الجرام = صافي المبيعات / مجموع الوزن (صافي)
        $avgPricePerGram = ($totalWeightSales > 0) ? ($netSales / $totalWeightSales) : 0;
        // الإجمالي = مجموع المبيعات + مجموع الضريبة (مبيعات) - مجموع المرتجعات + مجموع الضريبة (مرتجعات)
        // الإجمالي = إجمالي المبيعات + إجمالي ضريبة المبيعات فقط
        // الإجمالي = صافي المبيعات + مجموع الضريبة
        // الإجمالي = صافي المبيعات + مجموع الضريبة (do not use مجموع المبيعات)
        $alIjmali = $netSales + $totalTax;
        $totalWeightAll = $totalWeight + $totalWeightReturns;
        // سعر الجرام = الإجمالي / مجموع الوزن (صافي)
        $priceOfGram = ($totalWeightSales > 0) ? ($alIjmali / $totalWeightSales) : 0;
        // Sum of each caliber's weight x its kasr price (سعر الكسر)
        $fa2ida_sum = 0;
        foreach ($reportCalibers as $caliber) {
            $fa2ida_sum += ($caliber['weight'] ?? 0) * ($caliber['price_per_gram'] ?? 0);
        }
        $reportData = [
            // إجمالي المبيعات: مجموع المبيعات (بدون أي خصم أو طرح)
            'al_ijmali' => $alIjmali,
            'fa2ida_sum' => $fa2ida_sum,
            'al_ijmali_minus_fa2ida_sum' => $alIjmali - $fa2ida_sum,
            'total_sales' => $totalSales,
            // صافي المبيعات: مجموع net_amount لكل المنتجات (بدون أي خصم أو طرح)
            'net_sales' => $totalNetSales,
            // ...existing code...
            'total_sales_and_returns' => $totalNetSales + $totalReturns,
            'total_returns' => $totalReturns,
            'calibers' => $reportCalibers,
            'total_amount' => $totalAmount,
            'total_weight' => $totalWeightSales - $totalWeightReturns,
            'total_weight_returns' => $totalWeightReturns,
            'avg_price_per_gram' => $avgPricePerGram,
            'expenses' => $expenses,
            'salaries' => $salaries,
            'interest_value' => $interestValue,
            'interest_amount' => $interestAmount,
            'total_expenses' => $expenses + $salaries + $interestAmount,
            // 'profit' => ($totalSales + $netTax) - $totalCalibersCost,
            'profit' => $fa2ida_sum,
            'net_profit' => $totalAmount - ($expenses + $salaries + $interestAmount),
            'total_tax' => $netTax,
            'total_tax_sales' => $totalTax,
            'total_tax_returns' => $totalTaxReturns,
            'price_of_gram' => $priceOfGram,
        ];
        $selectedBranch = $filters['branch_id'] ? Branch::find($filters['branch_id']) : null;

        return view('reports.kasr', compact('branches', 'filters', 'reportData', 'selectedBranch', 'expenses', 'expensesList', 'salaries', 'salariesList', 'weights', 'calibers'));

    }

    /**
     * Comparative report (stub implementation)
     */
    public function comparative(Request $request)
    {
        // $this->enforceDeviceToken($request); // Removed: method does not exist
        // DEBUG: Show products array for first sale
        // $sale = \App\Models\Sale::notReturned()->first();
        // dd($sale ? $sale->products : null);

        $branches = Branch::active()->get();
        $calibers = Caliber::active()->get();
        $filters = [];

        // Aggregate sales and weights from products JSON for each branch
        $allBranches = Branch::active()->get();
        $sales = \App\Models\Sale::notReturned()->get();
        $branchSums = [];
        // Preload all expenses for all branches, filtered by date if provided
        $allExpensesQuery = \App\Models\Expense::query();
        if ($request->filled('from')) {
            $allExpensesQuery->whereDate('expense_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $allExpensesQuery->whereDate('expense_date', '<=', $request->input('to'));
        }
        $allExpenses = $allExpensesQuery->get();
        foreach ($sales as $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            if ($products) {
                foreach ($products as $product) {
                    $branchId = $sale->branch_id;
                    $amount = isset($product['amount']) ? (float) $product['amount'] : 0;
                    $weight = isset($product['weight']) ? (float) $product['weight'] : 0;
                    if (! isset($branchSums[$branchId])) {
                        $branchSums[$branchId] = [
                            'branch_id' => $branchId,
                            'branch_name' => optional($allBranches->firstWhere('id', $branchId))->name,
                            'total_sales' => 0,
                            'total_weight' => 0,
                            'sales_count' => 0,
                            'total_expenses' => 0,
                            'profit' => 0,
                        ];
                    }
                    $branchSums[$branchId]['total_sales'] += $amount;
                    $branchSums[$branchId]['total_weight'] += $weight;
                    $branchSums[$branchId]['sales_count']++;
                }
            }
        }
        // Add expenses and profit for each branch
        foreach ($allBranches as $branch) {
            $branchId = $branch->id;
            $expenses = $allExpenses->where('branch_id', $branchId)->sum('amount');
            if (! isset($branchSums[$branchId])) {
                $branchSums[$branchId] = [
                    'branch_id' => $branchId,
                    'branch_name' => $branch->name,
                    'total_sales' => 0,
                    'total_weight' => 0,
                    'sales_count' => 0,
                    'total_expenses' => $expenses,
                    'profit' => 0, // No sales, so profit is 0
                    // For chart.js single-bar charts
                    'sales' => 0,
                    'expenses' => $expenses,
                    // Add empty chart data for empty branches
                    'chart_sales' => [date('Y-m-d') => 0],
                    'chart_expenses' => [date('Y-m-d') => 0],
                ];
            } else {
                $branchSums[$branchId]['total_expenses'] = $expenses;
                $branchSums[$branchId]['sales'] = $branchSums[$branchId]['total_sales'];
                $branchSums[$branchId]['expenses'] = $expenses;
                $branchSums[$branchId]['profit'] = $branchSums[$branchId]['total_sales'] - $expenses;
                // If chart data is missing, add empty chart data
                if (empty($branchSums[$branchId]['chart_sales'])) {
                    $branchSums[$branchId]['chart_sales'] = [date('Y-m-d') => 0];
                }
                if (empty($branchSums[$branchId]['chart_expenses'])) {
                    $branchSums[$branchId]['chart_expenses'] = [date('Y-m-d') => 0];
                }
            }
        }
        $branchesComparison = collect(array_values($branchSums));

        // Aggregate sales and weights from products JSON for each employee per branch
        $allEmployees = \App\Models\Employee::all();
        $employeeSums = [];
        foreach ($sales as $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            if ($products) {
                foreach ($products as $product) {
                    $branchId = $sale->branch_id;
                    $employeeId = $sale->employee_id;
                    $amount = isset($product['amount']) ? (float) $product['amount'] : 0;
                    $weight = isset($product['weight']) ? (float) $product['weight'] : 0;
                    $key = $employeeId.'-'.$branchId;
                    if (! isset($employeeSums[$key])) {
                        $employeeSums[$key] = [
                            'employee_id' => $employeeId,
                            'employee_name' => optional($allEmployees->firstWhere('id', $employeeId))->name,
                            'branch_id' => $branchId,
                            'total_sales' => 0,
                            'total_weight' => 0,
                            'sales_count' => 0,
                        ];
                    }
                    $employeeSums[$key]['total_sales'] += $amount;
                    $employeeSums[$key]['total_weight'] += $weight;
                    $employeeSums[$key]['sales_count']++;
                }
            }
        }
        // Fill missing employee-branch pairs with zeros so all employees are shown for each branch
        foreach ($allBranches as $branch) {
            $branchEmployees = $allEmployees->where('branch_id', $branch->id);
            foreach ($branchEmployees as $employee) {
                $key = $employee->id.'-'.$branch->id;
                if (! isset($employeeSums[$key])) {
                    $employeeSums[$key] = [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'branch_id' => $branch->id,
                        'total_sales' => 0,
                        'total_weight' => 0,
                        'sales_count' => 0,
                    ];
                }
            }
        }
        $employeesComparison = collect(array_values($employeeSums));

        // Aggregate sales, weights, and count by category from products JSON
        $allCategories = \App\Models\Category::all();
        $categorySums = [];
        foreach ($sales as $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            if ($products) {
                foreach ($products as $product) {
                    if (! isset($product['category_id'])) {
                        continue;
                    }
                    $catId = $product['category_id'];
                    $branchId = $sale->branch_id;
                    $amount = isset($product['amount']) ? (float) $product['amount'] : 0;
                    $weight = isset($product['weight']) ? (float) $product['weight'] : 0;
                    $key = $catId.'-'.$branchId;
                    if (! isset($categorySums[$key])) {
                        $categorySums[$key] = [
                            'category_id' => $catId,
                            'branch_id' => $branchId,
                            'category_name' => null,
                            'total_sales' => 0,
                            'total_weight' => 0,
                            'items_count' => 0,
                        ];
                    }
                    $categorySums[$key]['total_sales'] += $amount;
                    $categorySums[$key]['total_weight'] += $weight;
                    $categorySums[$key]['items_count']++;
                }
            }
        }
        // Show all categories for each branch (even with zero sales/weight)
        foreach ($allCategories as $category) {
            foreach ($allBranches as $branch) {
                $key = $category->id.'-'.$branch->id;
                if (! isset($categorySums[$key])) {
                    $categorySums[$key] = [
                        'category_id' => $category->id,
                        'branch_id' => $branch->id,
                        'category_name' => $category->name,
                        'total_sales' => 0,
                        'total_weight' => 0,
                        'items_count' => 0,
                    ];
                } else {
                    $categorySums[$key]['category_name'] = $category->name;
                }
            }
        }
        $categoriesComparison = collect(array_values($categorySums));

        // Payment Methods Comparison
        $paymentMethods = [];
        foreach ($sales as $sale) {
            $method = $sale->payment_method ?? 'غير محدد';
            if (! isset($paymentMethods[$method])) {
                $paymentMethods[$method] = [
                    'name' => $method,
                    'amount' => 0,
                    'count' => 0,
                ];
            }
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            $saleAmount = 0;
            if ($products) {
                foreach ($products as $product) {
                    $saleAmount += isset($product['amount']) ? (float) $product['amount'] : 0;
                }
            }
            $paymentMethods[$method]['amount'] += $saleAmount;
            $paymentMethods[$method]['count']++;
        }
        $paymentMethodsComparison = collect(array_values($paymentMethods));

        // Aggregate calibers per branch
        $allCalibers = \App\Models\Caliber::all();
        $caliberSums = [];
        foreach ($sales as $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            if ($products) {
                foreach ($products as $product) {
                    if (! isset($product['caliber_id'])) {
                        continue;
                    }
                    $caliberId = $product['caliber_id'];
                    $branchId = $sale->branch_id;
                    $amount = isset($product['amount']) ? (float) $product['amount'] : 0;
                    $key = $caliberId.'-'.$branchId;
                    if (! isset($caliberSums[$key])) {
                        $caliberSums[$key] = [
                            'caliber_id' => $caliberId,
                            'branch_id' => $branchId,
                            'name' => null,
                            'total_sales' => 0,
                            'items_count' => 0,
                        ];
                    }
                    $caliberSums[$key]['total_sales'] += $amount;
                    $caliberSums[$key]['items_count']++;
                }
            }
        }
        // Fill missing caliber-branch pairs with zeros
        foreach ($allCalibers as $caliber) {
            foreach ($allBranches as $branch) {
                $key = $caliber->id.'-'.$branch->id;
                if (! isset($caliberSums[$key])) {
                    $caliberSums[$key] = [
                        'caliber_id' => $caliber->id,
                        'branch_id' => $branch->id,
                        'name' => $caliber->name,
                        'total_sales' => 0,
                        'items_count' => 0,
                    ];
                } else {
                    $caliberSums[$key]['name'] = $caliber->name;
                }
            }
        }

        $calibersComparison = collect(array_values($caliberSums));
        $showTwoBranchComparison = false;
        $twoBranchComparison = null;
        if ($request->filled('branch1') && $request->filled('branch2') && $request->input('branch1') != $request->input('branch2')) {
            $showTwoBranchComparison = true;
            $branch1 = $branchesComparison->firstWhere('branch_id', (int) $request->input('branch1'));
            $branch2 = $branchesComparison->firstWhere('branch_id', (int) $request->input('branch2'));
            if ($branch1 && $branch2) {
                $twoBranchComparison = [
                    'branch1' => $branch1,
                    'branch2' => $branch2,
                ];
            }
        }

        // Prepare employees grouped by branch
        $employeesByBranch = [];
        foreach ($branches as $branch) {
            $employeesByBranch[$branch->id] = $employeesComparison->where('branch_id', $branch->id)->values();
        }

        // Pass all branch IDs for use in the view
        $branchIds = $branches->pluck('id')->values();

        $data = compact('branches', 'calibers', 'filters', 'branchesComparison', 'employeesComparison', 'categoriesComparison', 'calibersComparison', 'showTwoBranchComparison', 'twoBranchComparison', 'employeesByBranch', 'branchIds', 'paymentMethodsComparison');

        return view('reports.comparative', $data);
    }

    /**
     * Generate PDF report.
     */
    private function generatePDF($view, $data, $filename)
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename.'_'.date('Y-m-d').'.pdf');
    }
}
