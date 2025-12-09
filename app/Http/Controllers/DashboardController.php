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

        $totalSales = $salesQuery->sum('total_amount');
        $totalWeight = $salesQuery->sum('weight');
        
        return [
            'total_sales' => $totalSales,
            'total_net_sales' => $salesQuery->sum('net_amount'),
            'total_tax' => $salesQuery->sum('tax_amount'),
            'total_weight' => $totalWeight,
            'total_expenses' => $expensesQuery->sum('amount'),
            'sales_count' => $salesQuery->count(),
            'expenses_count' => $expensesQuery->count(),
            'net_profit' => $salesQuery->sum('net_amount') - $expensesQuery->sum('amount'),
            'price_per_gram' => $totalWeight > 0 ? $totalSales / $totalWeight : 0,
        ];
    }

    /**
     * Get charts data for visualization.
     */
    private function getChartsData($startDate, $endDate, $branchId = null)
    {
        // Daily sales chart
        $dailySales = Sale::notReturned()
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount, SUM(weight) as weight')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly revenue data (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $sales = Sale::notReturned()->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
            $expenses = Expense::whereBetween('expense_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->sum('amount');
            $monthlyRevenue[] = [
                'month' => $monthStart->locale('ar')->isoFormat('MMM YYYY'),
                'sales' => $sales,
                'expenses' => $expenses,
            ];
        }

        // Sales by caliber - skip sales without caliber (multi-product sales)
        $salesByCaliber = Sale::notReturned()
            ->with('caliber')
            ->whereNotNull('caliber_id')
            ->selectRaw('caliber_id, SUM(total_amount) as amount, SUM(weight) as weight')
            ->groupBy('caliber_id')
            ->get()
            ->map(function ($item) {
                return [
                    'caliber' => $item->caliber?->name ?? 'غير محدد',
                    'amount' => $item->amount,
                    'weight' => $item->weight,
                ];
            });

        // Sales by category - skip sales without category (multi-product sales)
        $salesByCategory = Sale::notReturned()
            ->with('category')
            ->whereNotNull('category_id')
            ->selectRaw('category_id, SUM(total_amount) as amount, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name ?? 'غير محدد',
                    'amount' => $item->amount,
                    'count' => $item->count,
                ];
            });

        // Sales by branch
        $salesByBranch = Sale::notReturned()
            ->with('branch')
            ->selectRaw('branch_id, SUM(total_amount) as amount, COUNT(*) as count')
            ->groupBy('branch_id')
            ->get()
            ->map(function ($item) {
                return [
                    'branch' => $item->branch?->name ?? 'غير محدد',
                    'amount' => $item->amount,
                    'count' => $item->count,
                ];
            });

        // Expenses by type
        $expensesByType = Expense::with('expenseType')
            ->selectRaw('expense_type_id, SUM(amount) as amount, COUNT(*) as count')
            ->groupBy('expense_type_id')
            ->get()
            ->map(function ($item) {
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
        $topBranches = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->with('branch')
            ->selectRaw('branch_id, SUM(total_amount) as amount, SUM(weight) as weight, COUNT(*) as count')
            ->groupBy('branch_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        // Top employees by sales
        $topEmployees = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['employee', 'employee.branch'])
            ->selectRaw('employee_id, SUM(total_amount) as amount, SUM(weight) as weight, COUNT(*) as count')
            ->groupBy('employee_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        // Top categories by sales
        $topCategories = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('category')
            ->selectRaw('category_id, SUM(total_amount) as amount, SUM(weight) as weight, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        // Top calibers by sales
        $topCalibers = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('caliber')
            ->selectRaw('caliber_id, SUM(total_amount) as amount, SUM(weight) as weight, COUNT(*) as count')
            ->groupBy('caliber_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

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
        // Block chart data access for branch users
        if (auth()->check() && auth()->user()->isBranch()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $chartType = $request->get('chart_type');

        $chartsData = $this->getChartsData($startDate, $endDate);

        return response()->json($chartsData[$chartType] ?? []);
    }

    /**
     * Print-friendly dashboard report without charts.
     */
    public function print(Request $request)
    {
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