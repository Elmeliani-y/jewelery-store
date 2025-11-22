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
        // Restrict dashboard access for branch users
        if (auth()->check() && auth()->user()->isBranch()) {
            return redirect()->route('sales.create');
        }
        // Get date range (default to current month)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Key metrics
        $metrics = $this->getKeyMetrics($startDate, $endDate);
        
        // Charts data
        $chartsData = $this->getChartsData($startDate, $endDate);
        
        // Top performers
        $topPerformers = $this->getTopPerformers($startDate, $endDate);

        return view('dashboard.index', compact(
            'metrics',
            'chartsData', 
            'topPerformers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get key metrics for the dashboard.
     */
    private function getKeyMetrics($startDate, $endDate)
    {
        $salesQuery = Sale::notReturned()->inDateRange($startDate, $endDate);
        $expensesQuery = Expense::inDateRange($startDate, $endDate);

        return [
            'total_sales' => $salesQuery->sum('total_amount'),
            'total_net_sales' => $salesQuery->sum('net_amount'),
            'total_tax' => $salesQuery->sum('tax_amount'),
            'total_weight' => $salesQuery->sum('weight'),
            'total_expenses' => $expensesQuery->sum('amount'),
            'sales_count' => $salesQuery->count(),
            'expenses_count' => $expensesQuery->count(),
            'net_profit' => $salesQuery->sum('net_amount') - $expensesQuery->sum('amount'),
        ];
    }

    /**
     * Get charts data for visualization.
     */
    private function getChartsData($startDate, $endDate)
    {
        // Daily sales chart
        $dailySales = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount, SUM(weight) as weight')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly revenue data (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $sales = Sale::notReturned()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
            
            $expenses = Expense::whereBetween('expense_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => $monthStart->locale('ar')->isoFormat('MMM YYYY'),
                'sales' => $sales,
                'expenses' => $expenses,
            ];
        }

        // Sales by caliber
        $salesByCaliber = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->with('caliber')
            ->selectRaw('caliber_id, SUM(total_amount) as amount, SUM(weight) as weight')
            ->groupBy('caliber_id')
            ->get()
            ->map(function ($item) {
                return [
                    'caliber' => $item->caliber->name,
                    'amount' => $item->amount,
                    'weight' => $item->weight,
                ];
            });

        // Sales by category
        $salesByCategory = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->with('category')
            ->selectRaw('category_id, SUM(total_amount) as amount, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name,
                    'amount' => $item->amount,
                    'count' => $item->count,
                ];
            });

        // Sales by branch
        $salesByBranch = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->with('branch')
            ->selectRaw('branch_id, SUM(total_amount) as amount, COUNT(*) as count')
            ->groupBy('branch_id')
            ->get()
            ->map(function ($item) {
                return [
                    'branch' => $item->branch->name,
                    'amount' => $item->amount,
                    'count' => $item->count,
                ];
            });

        // Expenses by type
        $expensesByType = Expense::inDateRange($startDate, $endDate)
            ->with('expenseType')
            ->selectRaw('expense_type_id, SUM(amount) as amount, COUNT(*) as count')
            ->groupBy('expense_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->expenseType->name,
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
    private function getTopPerformers($startDate, $endDate)
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
            ->with(['employee', 'employee.branch'])
            ->selectRaw('employee_id, SUM(total_amount) as amount, SUM(weight) as weight, COUNT(*) as count')
            ->groupBy('employee_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        // Top categories by sales
        $topCategories = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
            ->with('category')
            ->selectRaw('category_id, SUM(total_amount) as amount, SUM(weight) as weight, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        // Top calibers by sales
        $topCalibers = Sale::notReturned()
            ->inDateRange($startDate, $endDate)
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
}