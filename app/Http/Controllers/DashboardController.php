<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Caliber;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard with analytics and insights.
     */
    public function index(Request $request)
    {
        $this->enforceDeviceToken($request);
        // Trusted device check logic (was middleware)
        if (auth()->check()) {
            // Exclude admin users from device trust check (admin is always trusted)
            if (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()) {
                // continue
            } else {
                // Allow access to pairing routes without device check (not needed here, only for pairing routes)
                $deviceToken = $request->cookie('device_token');
                if (!$deviceToken || !\App\Models\Device::where('token', $deviceToken)->where('user_id', auth()->id())->exists()) {
                    // If not trusted, redirect to pairing page
                    return redirect()->route('pair-device.form');
                }
            }
        }
        // Branch users: restrict to today and their branch. Others: allow filtering
        if (auth()->check() && auth()->user()->isBranch()) {
            $period = 'daily';
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $branchId = auth()->user()->branch_id;
        } else {
            // Admin users: use request parameters or default to monthly
            $period = $request->get('period', 'monthly');
            $branchId = $request->get('branch_id');
            
            // Calculate date range based on period
            switch ($period) {
                case 'daily':
                    $startDate = Carbon::now()->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    break;
                case 'weekly':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'monthly':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfDay();
                    break;
                case 'custom':
                    $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()))->startOfDay();
                    $endDate = Carbon::parse($request->get('end_date', Carbon::now()))->endOfDay();
                    break;
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfDay();
            }
        }

        // Key metrics
        $metrics = $this->getKeyMetrics($startDate, $endDate, $branchId);
        
        // Charts data
        $chartsData = $this->getChartsData($startDate, $endDate, $branchId);
        
        // Top performers
        $topPerformers = $this->getTopPerformers($startDate, $endDate, $branchId);

        $branches = \App\Models\Branch::active()->get();

        // CSV export of key metrics
        if ($request->get('format') === 'csv') {
            $filename = 'dashboard_metrics_' . date('Y-m-d') . '.csv';
            $startDateFormatted = $startDate->format('Y-m-d');
            $endDateFormatted = $endDate->format('Y-m-d');
            return response()->streamDownload(function () use ($metrics, $period, $startDateFormatted, $endDateFormatted, $branchId) {
                $out = fopen('php://output', 'w');
                // UTF-8 BOM for Excel
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['الفترة', $period === 'custom' ? ($startDateFormatted.' - '.$endDateFormatted) : $period]);
                fputcsv($out, ['الفرع', $branchId ?: 'كل الفروع']);
                fputcsv($out, []);
                fputcsv($out, ['المؤشر', 'القيمة']);
                foreach ($metrics as $key => $val) {
                    $label = match($key){
                        'total_sales' => 'إجمالي المبيعات',
                        'total_net_sales' => 'صافي المبيعات',
                        'total_tax' => 'إجمالي الضريبة',
                        'total_weight' => 'إجمالي الوزن',
                        'total_expenses' => 'إجمالي المصروفات',
                        'sales_count' => 'عدد الفواتير',
                        'expenses_count' => 'عدد المصروفات',
                        'net_profit' => 'صافي الربح',
                        default => $key,
                    };
                    fputcsv($out, [$label, $val]);
                }
                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        return view('dashboard.index', compact(
            'metrics',
            'chartsData', 
            'topPerformers',
            'period',
            'branchId',
            'branches'
        ))->with([
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Get key metrics for the dashboard.
     */
    private function getKeyMetrics($startDate, $endDate, $branchId = null)
    {
        $salesQuery = Sale::notReturned();
        $expensesQuery = Expense::query();
        if ($startDate && $endDate) {
            $salesQuery = $salesQuery->inDateRange($startDate, $endDate);
            $expensesQuery = $expensesQuery->inDateRange($startDate, $endDate);
        }
        if ($branchId) {
            $salesQuery = $salesQuery->where('branch_id', $branchId);
            $expensesQuery = $expensesQuery->where('branch_id', $branchId);
        }

        // Calculate true gross sales from all product amounts (not just total_amount column)
        $totalSales = $salesQuery->get()->reduce(function($carry, $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            $sum = 0;
            if ($products) {
                foreach ($products as $product) {
                    $sum += isset($product['amount']) ? (float)$product['amount'] : 0;
                }
            }
            return $carry + $sum;
        }, 0);
        // Sum weights from products JSON, not a column
        $totalWeight = $salesQuery->get()->reduce(function($carry, $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            $weight = 0;
            if ($products) {
                foreach ($products as $product) {
                    if (isset($product['weight'])) {
                        $weight += (float)$product['weight'];
                    }
                }
            }
            return $carry + $weight;
        }, 0);
        
        $returnedSalesQuery = Sale::query()->where('is_returned', true);
        if ($startDate && $endDate) {
            $returnedSalesQuery = $returnedSalesQuery->whereBetween('returned_at', [$startDate, $endDate]);
        }
        if ($branchId) {
            $returnedSalesQuery = $returnedSalesQuery->where('branch_id', $branchId);
        }
        // Calculate true gross returned sales from all product amounts
        $totalReturnedSales = $returnedSalesQuery->get()->reduce(function($carry, $sale) {
            $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
            $sum = 0;
            if ($products) {
                foreach ($products as $product) {
                    $sum += isset($product['amount']) ? (float)$product['amount'] : 0;
                }
            }
            return $carry + $sum;
        }, 0);
        $returnedSalesCount = $returnedSalesQuery->count();

        return [
            // Show إجمالي المبيعات (gross sales, sum of all product amounts for non-returned sales)
            'total_sales' => $totalSales,
            'gross_sales' => $totalSales, // For clarity, same as total_sales now
            'total_net_sales' => $salesQuery->sum('net_amount'),
            'total_tax' => $salesQuery->sum('tax_amount'),
            'total_weight' => $totalWeight,
            'total_expenses' => $expensesQuery->sum('amount'),
            'sales_count' => $salesQuery->count(),
            'expenses_count' => $expensesQuery->count(),
            'net_profit' => $salesQuery->sum('net_amount') - $expensesQuery->sum('amount'),
            'price_per_gram' => $totalWeight > 0 ? $totalSales / $totalWeight : 0,
            'returned_sales_count' => $returnedSalesCount,
            'returned_sales_total' => $totalReturnedSales,
        ];
    }

    /**
     * Get charts data for visualization.
     */
    private function getChartsData($startDate, $endDate, $branchId = null)
    {
        // Normalize date inputs if provided
        if ($startDate && $endDate) {
            $start = is_string($startDate) ? Carbon::parse($startDate)->startOfDay() : $startDate->startOfDay();
            $end = is_string($endDate) ? Carbon::parse($endDate)->endOfDay() : $endDate->endOfDay();
        } else {
            $start = null;
            $end = null;
        }

        // Daily sales chart (apply date range and branch filters when available)
        $dailyQuery = Sale::notReturned()
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount')
            ->groupBy('date')
            ->orderBy('date');
        if ($start && $end) {
            $dailyQuery->whereBetween('created_at', [$start, $end]);
        }
        if ($branchId) {
            $dailyQuery->where('branch_id', $branchId);
        }
        $dailySales = $dailyQuery->get()->map(function ($sale) use ($start, $end, $branchId) {
            // Sum weights from products JSON for each day with same filters
            $salesForDayQuery = Sale::notReturned()->whereDate('created_at', $sale->date);
            if ($start && $end) {
                $salesForDayQuery->whereBetween('created_at', [$start, $end]);
            }
            if ($branchId) {
                $salesForDayQuery->where('branch_id', $branchId);
            }
            $salesForDay = $salesForDayQuery->get();
            $weight = 0;
            foreach ($salesForDay as $s) {
                $products = is_array($s->products) ? $s->products : json_decode($s->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        if (isset($product['weight'])) {
                            $weight += (float)$product['weight'];
                        }
                    }
                }
            }
            return [
                'date' => $sale->date,
                'amount' => $sale->amount,
                'weight' => $weight,
            ];
        });

        // Monthly revenue data (either from provided date range, or last 12 months)
        $monthlyRevenue = [];
        if ($start && $end) {
            // Build months between start and end inclusive
            $cursor = $start->copy()->startOfMonth();
            $endMonth = $end->copy()->endOfMonth();
            while ($cursor->lte($endMonth)) {
                $monthStart = $cursor->copy()->startOfMonth();
                $monthEnd = $cursor->copy()->endOfMonth();
                $salesQuery = Sale::notReturned()->whereBetween('created_at', [$monthStart, $monthEnd]);
                $expensesQuery = Expense::whereBetween('expense_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')]);
                if ($branchId) {
                    $salesQuery->where('branch_id', $branchId);
                    $expensesQuery->where('branch_id', $branchId);
                }
                // Calculate sales as the sum of product amounts (consistent with getKeyMetrics)
                $sales = $salesQuery->get()->reduce(function($carry, $sale) {
                    $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                    $sum = 0;
                    if ($products) {
                        foreach ($products as $product) {
                            $sum += isset($product['amount']) ? (float)$product['amount'] : 0;
                        }
                    }
                    return $carry + $sum;
                }, 0);
                $expenses = $expensesQuery->sum('amount');
                $monthlyRevenue[] = [
                    'month' => $monthStart->locale('ar')->isoFormat('MMM YYYY'),
                    'sales' => $sales,
                    'expenses' => $expenses,
                ];
                $cursor->addMonth();
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
                $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
                $salesQuery = Sale::notReturned()->whereBetween('created_at', [$monthStart, $monthEnd]);
                $expensesQuery = Expense::whereBetween('expense_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')]);
                if ($branchId) {
                    $salesQuery->where('branch_id', $branchId);
                    $expensesQuery->where('branch_id', $branchId);
                }
                // Calculate sales as the sum of product amounts (consistent with getKeyMetrics)
                $sales = $salesQuery->get()->reduce(function($carry, $sale) {
                    $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                    $sum = 0;
                    if ($products) {
                        foreach ($products as $product) {
                            $sum += isset($product['amount']) ? (float)$product['amount'] : 0;
                        }
                    }
                    return $carry + $sum;
                }, 0);
                $expenses = $expensesQuery->sum('amount');
                $monthlyRevenue[] = [
                    'month' => $monthStart->locale('ar')->isoFormat('MMM YYYY'),
                    'sales' => $sales,
                    'expenses' => $expenses,
                ];
            }
        }

        // Sales by caliber - skip sales without caliber (multi-product sales)
        $salesByCaliber = collect();
        $calibers = \App\Models\Caliber::all();
        $salesQuery = Sale::notReturned()->whereNotNull('products');
        if ($start && $end) {
            $salesQuery->whereBetween('created_at', [$start, $end]);
        }
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }
        $sales = $salesQuery->get();
        foreach ($calibers as $caliber) {
            $amount = 0;
            $weight = 0;
            foreach ($sales as $sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                if ($products) {
                    foreach ($products as $product) {
                        if (isset($product['caliber_id']) && $product['caliber_id'] == $caliber->id) {
                            $amount += $product['amount'] ?? 0;
                            $weight += $product['weight'] ?? 0;
                        }
                    }
                }
            }
            $salesByCaliber->push([
                'caliber' => $caliber->name,
                'amount' => $amount,
                'weight' => $weight,
            ]);
        }

        // Sales by category - from products JSON
        $salesByCategory = collect();
        $categories = \App\Models\Category::all();
        
        foreach ($categories as $category) {
            $salesQuery = Sale::notReturned()->whereNotNull('products');
            if ($start && $end) {
                $salesQuery->whereBetween('created_at', [$start, $end]);
            }
            if ($branchId) {
                $salesQuery->where('branch_id', $branchId);
            }
            $sales = $salesQuery->get();
            $totalAmount = 0;
            $totalWeight = 0;
            $count = 0;
            foreach ($sales as $sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                if ($products) {
                    foreach ($products as $product) {
                        if (isset($product['category_id']) && $product['category_id'] == $category->id) {
                            $totalAmount += $product['amount'] ?? 0;
                            $totalWeight += $product['weight'] ?? 0;
                            $count++;
                        }
                    }
                }
            }
            if ($count > 0) {
                $salesByCategory->push([
                    'category' => $category->name,
                    'amount' => $totalAmount,
                    'weight' => $totalWeight,
                    'count' => $count,
                ]);
            }
        }

        // Sales by branch
        $salesByBranchQuery = Sale::notReturned()->with('branch')
            ->selectRaw('branch_id, SUM(total_amount) as amount, COUNT(*) as count')
            ->groupBy('branch_id');
        if ($start && $end) {
            $salesByBranchQuery->whereBetween('created_at', [$start, $end]);
        }
        if ($branchId) {
            $salesByBranchQuery->where('branch_id', $branchId);
        }
        $salesByBranch = $salesByBranchQuery->get()->map(function ($item) {
            return [
                'branch' => $item->branch?->name ?? 'غير محدد',
                'amount' => $item->amount,
                'count' => $item->count,
            ];
        });

        // Expenses by type
        $expensesQuery = Expense::with('expenseType')
            ->selectRaw('expense_type_id, SUM(amount) as amount, COUNT(*) as count')
            ->groupBy('expense_type_id');
        if ($start && $end) {
            $expensesQuery->whereBetween('expense_date', [$start->format('Y-m-d'), $end->format('Y-m-d')]);
        }
        if ($branchId) {
            $expensesQuery->where('branch_id', $branchId);
        }
        $expensesByType = $expensesQuery->get()->map(function ($item) {
            return [
                'type' => $item->expenseType?->name ?? 'غير محدد',
                'amount' => $item->amount,
                'count' => $item->count,
            ];
        });

        return [
            'daily_sales' => $dailySales,
            'monthly_revenue' => $monthlyRevenue,
            'sales_by_caliber' => $salesByCaliber,
            'sales_by_category' => $salesByCategory,
            'sales_by_branch' => $salesByBranch,
            'expenses_by_type' => $expensesByType,
        ];
    }

    /**
     * Get top performers data.
     */
    private function getTopPerformers($startDate, $endDate, $branchId = null)
    {
        // Top branches by sales
        $branchSales = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->with('branch')
            ->get();
        $branchData = [];
        foreach ($branchSales as $sale) {
            $branchId = $sale->branch_id;
            if (!isset($branchData[$branchId])) {
                $branchData[$branchId] = [
                    'branch_id' => $branchId,
                    'amount' => 0,
                    'weight' => 0,
                    'count' => 0,
                    'branch' => $sale->branch,
                ];
            }
            $branchData[$branchId]['amount'] += $sale->total_amount;
            $branchData[$branchId]['count']++;
            $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
            if (is_array($products)) {
                foreach ($products as $product) {
                    $branchData[$branchId]['weight'] += $product['weight'] ?? 0;
                }
            }
        }
        $topBranches = collect($branchData)->sortByDesc('amount')->take(5)->values();

        // Top employees by sales
        $employeeSales = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['employee', 'employee.branch'])
            ->get();
        $employeeData = [];
        foreach ($employeeSales as $sale) {
            $employeeId = $sale->employee_id;
            if (!isset($employeeData[$employeeId])) {
                $employeeData[$employeeId] = [
                    'employee_id' => $employeeId,
                    'amount' => 0,
                    'weight' => 0,
                    'count' => 0,
                    'employee' => $sale->employee,
                ];
            }
            $employeeData[$employeeId]['amount'] += $sale->total_amount;
            $employeeData[$employeeId]['count']++;
            $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
            if (is_array($products)) {
                foreach ($products as $product) {
                    $employeeData[$employeeId]['weight'] += $product['weight'] ?? 0;
                }
            }
        }
        $topEmployees = collect($employeeData)->sortByDesc('amount')->take(5)->values();

        // Top categories by sales - parse from products JSON
        $sales = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $categoryData = [];
        foreach ($sales as $sale) {
            $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
            if (is_array($products)) {
                foreach ($products as $product) {
                    $categoryId = $product['category_id'] ?? null;
                    if ($categoryId) {
                        if (!isset($categoryData[$categoryId])) {
                            $categoryData[$categoryId] = [
                                'category_id' => $categoryId,
                                'category_name' => $product['category_name'] ?? '',
                                'amount' => 0,
                                'weight' => 0,
                                'count' => 0,
                            ];
                        }
                        $categoryData[$categoryId]['amount'] += $product['amount'] ?? 0;
                        $categoryData[$categoryId]['weight'] += $product['weight'] ?? 0;
                        $categoryData[$categoryId]['count']++;
                    }
                }
            }
        }

        // Sort by amount and take top 5
        usort($categoryData, fn($a, $b) => $b['amount'] <=> $a['amount']);
        $topCategories = collect(array_slice($categoryData, 0, 5))->map(function($item) {
            return (object) [
                'category_id' => $item['category_id'],
                'category' => Category::find($item['category_id']),
                'amount' => $item['amount'],
                'weight' => $item['weight'],
                'count' => $item['count'],
            ];
        });

        // Top calibers by sales - aggregate from products JSON
        $sales = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $caliberData = [];
        foreach ($sales as $sale) {
            $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
            if (is_array($products)) {
                foreach ($products as $product) {
                    $caliberId = $product['caliber_id'] ?? null;
                    if ($caliberId) {
                        if (!isset($caliberData[$caliberId])) {
                            $caliberData[$caliberId] = [
                                'caliber_id' => $caliberId,
                                'amount' => 0,
                                'weight' => 0,
                                'count' => 0,
                                'caliber' => null,
                            ];
                        }
                        $caliberData[$caliberId]['amount'] += $product['amount'] ?? 0;
                        $caliberData[$caliberId]['weight'] += $product['weight'] ?? 0;
                        $caliberData[$caliberId]['count']++;
                    }
                }
            }
        }
        // Attach caliber model
        foreach ($caliberData as $caliberId => &$data) {
            $data['caliber'] = \App\Models\Caliber::find($caliberId);
        }
        unset($data);
        // Sort by amount and take top 5
        usort($caliberData, fn($a, $b) => $b['amount'] <=> $a['amount']);
        $topCalibers = collect(array_slice($caliberData, 0, 5))->map(function($item) {
            return (object) [
                'caliber_id' => $item['caliber_id'],
                'caliber' => $item['caliber'],
                'amount' => $item['amount'],
                'weight' => $item['weight'],
                'count' => $item['count'],
            ];
        });

        return [
            'branches' => $topBranches,
            'employees' => $topEmployees,
            'categories' => $topCategories,
            'calibers' => $topCalibers,
        ];
    }

    /**
     * Get dashboard data via AJAX for chart updates.
     */
    public function getChartData(Request $request)
    {
        $this->enforceDeviceToken($request);
        // Block chart data access for branch users
        if (auth()->check() && auth()->user()->isBranch()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $chartType = $request->get('chart_type');
        $branchId = $request->get('branch_id');

        $chartsData = $this->getChartsData($startDate, $endDate, $branchId);

        return response()->json($chartsData[$chartType] ?? []);
    }

    /**
     * Print-friendly dashboard report without charts.
     */
    public function print(Request $request)
    {
        $this->enforceDeviceToken($request);
        if (auth()->check() && auth()->user()->isBranch()) {
            return redirect()->route('sales.create');
        }

        $period = $request->get('period', 'monthly');
        $branchId = $request->get('branch_id');

        if ($period === 'daily') {
            $startDate = Carbon::now()->startOfDay()->format('Y-m-d');
            $endDate = Carbon::now()->endOfDay()->format('Y-m-d');
        } elseif ($period === 'weekly') {
            $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($period === 'monthly') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        }

        $metrics = $this->getKeyMetrics($startDate, $endDate, $branchId);
        $chartsData = $this->getChartsData($startDate, $endDate, $branchId);
        $topPerformers = $this->getTopPerformers($startDate, $endDate, $branchId);
        $branch = $branchId ? Branch::find($branchId) : null;

        return view('dashboard.print', compact(
            'metrics', 'chartsData', 'topPerformers', 'startDate', 'endDate', 'period', 'branch'
        ));
    }
}