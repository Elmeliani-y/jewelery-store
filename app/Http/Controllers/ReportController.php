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
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    /**
     * Display reports index with filtering options.
     */
    public function index()
    {
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
        $filters = $this->validateFilters($request);
        
        // Get sales data
        $salesQuery = $this->buildSalesQuery($filters);
        $sales = $salesQuery->with(['branch', 'employee', 'category', 'caliber'])->get();
        
        // Get expenses data  
        $expensesQuery = $this->buildExpensesQuery($filters);
        $expenses = $expensesQuery->with(['branch', 'expenseType'])->get();

        // Calculate summaries
        $summary = [
            'total_sales' => $sales->sum('total_amount'),
            'total_net_sales' => $sales->sum('net_amount'),
            'total_tax' => $sales->sum('tax_amount'),
            'total_weight' => $sales->sum('weight'),
            'total_expenses' => $expenses->sum('amount'),
            'net_profit' => $sales->sum('net_amount') - $expenses->sum('amount'),
            'sales_count' => $sales->count(),
            'expenses_count' => $expenses->count(),
        ];

        $data = compact('sales', 'expenses', 'summary', 'filters');

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.comprehensive', $data, 'تقرير شامل');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'comprehensive_report.xlsx');
        }

        return view('reports.comprehensive', $data);
    }

    /**
     * Generate detailed sales report.
     */
    public function detailed(Request $request)
    {
        $filters = $this->validateFilters($request);
        
        $salesQuery = $this->buildSalesQuery($filters);
        $sales = $salesQuery->with(['branch', 'employee', 'category', 'caliber'])->get();

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
            'total_weight' => $sales->sum('weight'),
            'sales_count' => $sales->count(),
        ];

        $data = compact('sales', 'groupedData', 'summary', 'filters');

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.detailed', $data, 'تقرير مفصل');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'detailed_report.xlsx');
        }

        return view('reports.detailed', $data);
    }

    /**
     * Generate calibers report.
     */
    public function calibers(Request $request)
    {
        $filters = $this->validateFilters($request);
        
        $calibersData = Caliber::active()
            ->withCount(['sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }])
            ->with(['sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }])
            ->get()
            ->map(function ($caliber) {
                return [
                    'caliber' => $caliber,
                    'total_amount' => $caliber->sales->sum('total_amount'),
                    'total_weight' => $caliber->sales->sum('weight'),
                    'total_tax' => $caliber->sales->sum('tax_amount'),
                    'net_amount' => $caliber->sales->sum('net_amount'),
                    'sales_count' => $caliber->sales_count,
                ];
            });

        $data = compact('calibersData', 'filters');

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.calibers', $data, 'تقرير العيارات');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'calibers_report.xlsx');
        }

        return view('reports.calibers', $data);
    }

    /**
     * Generate categories report.
     */
    public function categories(Request $request)
    {
        $filters = $this->validateFilters($request);
        
        $categoriesData = Category::active()
            ->withCount(['sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }])
            ->with(['sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'category' => $category,
                    'total_amount' => $category->sales->sum('total_amount'),
                    'total_weight' => $category->sales->sum('weight'),
                    'sales_count' => $category->sales_count,
                ];
            });

        $data = compact('categoriesData', 'filters');

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.categories', $data, 'تقرير الأصناف');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'categories_report.xlsx');
        }

        return view('reports.categories', $data);
    }

    /**
     * Generate employees report.
     */
    public function employees(Request $request)
    {
        $filters = $this->validateFilters($request);
        
        $employeesData = Employee::active()
            ->with(['branch', 'sales' => function ($query) use ($filters) {
                $this->applySalesFilters($query, $filters);
                $query->where('is_returned', false);
            }])
            ->get()
            ->map(function ($employee) {
                return [
                    'employee' => $employee,
                    'total_sales' => $employee->sales->sum('total_amount'),
                    'total_weight' => $employee->sales->sum('weight'),
                    'sales_count' => $employee->sales->count(),
                    'net_profit' => $employee->sales->sum('net_amount') - $employee->salary,
                ];
            });

        $data = compact('employeesData', 'filters');

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.employees', $data, 'تقرير الموظفين');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'employees_report.xlsx');
        }

        return view('reports.employees', $data);
    }

    /**
     * Generate net profit report after deducting wages and salaries.
     */
    public function netProfit(Request $request)
    {
        $filters = $this->validateFilters($request);
        
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
        ];

        if ($request->get('format') === 'pdf') {
            return $this->generatePDF('reports.net_profit', $data, 'تقرير صافي الربح');
        }

        if ($request->get('format') === 'excel') {
            return Excel::download(new ReportsExport($data), 'net_profit_report.xlsx');
        }

        return view('reports.net_profit', $data);
    }

    /**
     * Build sales query with filters.
     */
    private function buildSalesQuery($filters)
    {
        $query = Sale::notReturned();

        return $this->applySalesFilters($query, $filters);
    }

    /**
     * Apply sales filters to query.
     */
    private function applySalesFilters($query, $filters)
    {
        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['caliber_id'])) {
            $query->where('caliber_id', $filters['caliber_id']);
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

        if ($request->filled('branch_id')) {
            $filters['branch_id'] = $request->branch_id;
        }

        if ($request->filled('employee_id')) {
            $filters['employee_id'] = $request->employee_id;
        }

        if ($request->filled('category_id')) {
            $filters['category_id'] = $request->category_id;
        }

        if ($request->filled('caliber_id')) {
            $filters['caliber_id'] = $request->caliber_id;
        }

        if ($request->filled('expense_type_id')) {
            $filters['expense_type_id'] = $request->expense_type_id;
        }

        $filters['date_from'] = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $filters['date_to'] = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        return $filters;
    }

    /**
     * Generate PDF report.
     */
    private function generatePDF($view, $data, $filename)
    {
        $pdf = Pdf::loadView($view, $data)
                 ->setPaper('a4', 'portrait')
                 ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($filename . '_' . date('Y-m-d') . '.pdf');
    }
}