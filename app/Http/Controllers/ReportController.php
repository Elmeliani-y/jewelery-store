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
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    // ...existing code...
    /**
     * Generate report by branch (sales and expenses grouped by branch).
     */

    public function byBranch(Request $request)
    {
        $filters = $this->validateFilters($request);
        $lists = $this->getFilterLists();
        $format = $request->get('format');
        $perPage = (int) $request->get('per_page', 25);

        // Get all branches (active)
        $branches = Branch::active()->get();

        // For each branch, get sales and expenses summary in the filter range
        $branchData = $branches->map(function($branch) use ($filters) {
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
        $branchData = $branchesCollection->map(function($branch) use ($filters) {
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
                        $totalWeight += isset($product['weight']) ? (float)$product['weight'] : 0;
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
            $netAmount = $salesQuery->sum('net_amount');
            return [
                'employee' => $employee,
                'total_sales' => $salesQuery->sum('total_amount'),
                'total_weight' => $salesQuery->sum('weight'),
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
            'total_sales' => $totalSales,
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
            'by_branch' => $sales->groupBy('branch.name')->map(function($group) {
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
            'by_employee' => $sales->groupBy('employee.name')->map(function($group) {
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
            'by_category' => $sales->flatMap(function($sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                $result = collect();
                if ($products) {
                    foreach ($products as $product) {
                        $result->push(['category_id' => $product['category_id'] ?? null, 'weight' => $product['weight'] ?? 0]);
                    }
                }
                return $result;
            })->groupBy('category_id'),
            'by_caliber' => $sales->flatMap(function($sale) {
                $products = is_string($sale->products) ? json_decode($sale->products, true) : $sale->products;
                $result = collect();
                if ($products) {
                    foreach ($products as $product) {
                        $result->push(['caliber_id' => $product['caliber_id'] ?? null, 'weight' => $product['weight'] ?? 0]);
                    }
                }
                return $result;
            })->groupBy('caliber_id'),
            'by_date' => $sales->groupBy(function ($sale) { return $sale->created_at->format('Y-m-d'); }),
        ];

        // Pass all lists for filters
        $branches = $lists['branches'];
        $employees = $lists['employees'];
        $categories = $lists['categories'];
        $calibers = $lists['calibers'];
        $expenseTypes = $lists['expenseTypes'];

        // Load minimum price setting from database
        $minGramPrice = (float)\App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 2.0));

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
            SUM(weight) as weight,
            SUM(cash_amount) as cash,
            SUM(network_amount) as network
        ')->first();
        
        $expensesStats = $expensesQuery->selectRaw('
            COUNT(*) as count,
            SUM(amount) as total
        ')->first();
        
        // Calculate derived metrics
        $profit = ($salesStats->net ?? 0) - ($expensesStats->total ?? 0);
        $pricePerGram = ($salesStats->weight ?? 0) > 0 ? ($salesStats->total ?? 0) / $salesStats->weight : 0;
        
        // Top 5 employees (fast query)
        $topEmployees = DB::table('sales')
            ->join('employees', 'sales.employee_id', '=', 'employees.id')
            ->where('sales.is_returned', false)
            ->whereDate('sales.created_at', $date)
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->selectRaw('employees.name, COUNT(*) as sales_count, SUM(sales.total_amount) as total_sales, SUM(sales.weight) as total_weight')
            ->groupBy('employees.id', 'employees.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
        
        // Sales by caliber (fast query)
        $salesByCaliber = DB::table('sales')
            ->join('calibers', 'sales.caliber_id', '=', 'calibers.id')
            ->where('sales.is_returned', false)
            ->whereDate('sales.created_at', $date)
            ->when($branchId, fn($q) => $q->where('sales.branch_id', $branchId))
            ->selectRaw('calibers.name, COUNT(*) as count, SUM(sales.total_amount) as amount, SUM(sales.weight) as weight')
            ->groupBy('calibers.id', 'calibers.name')
            ->orderByDesc('amount')
            ->get();
        
        // Payment methods breakdown
        $paymentMethods = DB::table('sales')
            ->where('is_returned', false)
            ->whereDate('created_at', $date)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as amount')
            ->groupBy('payment_method')
            ->get();
        
        // Top expense types
        $topExpenseTypes = DB::table('expenses')
            ->join('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
            ->whereDate('expenses.expense_date', $date)
            ->when($branchId, fn($q) => $q->where('expenses.branch_id', $branchId))
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
            'sales_weight' => $salesStats->weight ?? 0,
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
            return $this->generatePDF('reports.speed', $data, 'تقرير_سريع_' . $date);
        }
        if ($format === 'csv') {
            $filename = 'speed_report_' . $date . '.csv';
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
                fputcsv($out, ['صافي الربح', $metrics['profit']]);
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
            'total_sales' => (clone $salesQuery)->sum('total_amount'),
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
                        'total_weight' => $caliber->sales->sum('weight'),
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
                    'total_weight' => $employee->sales->sum('weight'),
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
        $minGramPrice = (float)\App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 2.0));

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
        $branches = Branch::active()->get();
        $filters = [
            'branch_id' => $request->get('branch_id'),
            'date_from' => $request->get('date_from', date('Y-m-01')),
            'date_to' => $request->get('date_to', date('Y-m-d')),
        ];

        // Auto-load expenses and salaries from database
        $expenses = 0;
        $salaries = 0;
        
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            // Get all expenses (الأجور) - excluding salary type
            $salaryExpenseType = ExpenseType::where('name', 'like', '%رواتب%')->first();
            
            $expensesQuery = Expense::where('branch_id', $filters['branch_id'])
                ->whereDate('expense_date', '>=', $filters['date_from'])
                ->whereDate('expense_date', '<=', $filters['date_to']);
            
            if ($salaryExpenseType) {
                // Expenses excluding salaries
                $expenses = (clone $expensesQuery)->where('expense_type_id', '!=', $salaryExpenseType->id)->sum('amount');
                // Salaries only
                $salaries = (clone $expensesQuery)->where('expense_type_id', $salaryExpenseType->id)->sum('amount');
            } else {
                // All expenses if no salary type found
                $expenses = $expensesQuery->sum('amount');
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
        if ($filters['branch_id'] && $filters['date_from'] && $filters['date_to']) {
            $sales = \App\Models\Sale::notReturned()
                ->where('branch_id', $filters['branch_id'])
                ->whereDate('created_at', '>=', $filters['date_from'])
                ->whereDate('created_at', '<=', $filters['date_to'])
                ->get();
            // Debug: Log sales and products
            \Log::info('KASR SALES', ['sales' => $sales->toArray()]);
            foreach ($sales as $sale) {
                $products = is_array($sale->products) ? $sale->products : json_decode($sale->products, true);
                \Log::info('KASR PRODUCTS', ['sale_id' => $sale->id, 'products' => $products]);
                if ($products) {
                    foreach ($products as $product) {
                        // Try to match by caliber_name if caliber_id does not exist in weights
                        $cid = isset($product['caliber_id']) ? (int)$product['caliber_id'] : null;
                        $cname = isset($product['caliber_name']) ? trim($product['caliber_name']) : null;
                        $targetId = null;
                        if ($cid && isset($weights[$cid])) {
                            $targetId = $cid;
                        } elseif ($cname && isset($caliberNameToId[$cname])) {
                            $targetId = $caliberNameToId[$cname];
                        }
                        if ($targetId && isset($product['weight'])) {
                            $weights[$targetId] += (float)$product['weight'];
                        }
                    }
                }
            }
        }

        $prices = [];
        foreach ($calibers as $caliber) {
            $prices[$caliber->id] = (float) $request->get('price_' . $caliber->id, 0);
        }
        $reportCalibers = [];
        $totalAmount = 0;
        $totalWeight = 0;
        foreach ($calibers as $caliber) {
            $weight = $weights[$caliber->id];
            $price = $prices[$caliber->id];
            $amount = $weight * $price;
            $reportCalibers[] = [
                'name' => $caliber->name,
                'weight' => $weight,
                'price_per_gram' => $price,
                'amount' => $amount,
            ];
            $totalAmount += $amount;
            $totalWeight += $weight;
        }
        $reportData = [
            'calibers' => $reportCalibers,
            'total_amount' => $totalAmount,
            'total_weight' => $totalWeight,
            'avg_price_per_gram' => $totalWeight > 0 ? $totalAmount / $totalWeight : 0,
            'expenses' => $expenses,
            'salaries' => $salaries,
            'total_expenses' => $expenses + $salaries,
            'profit' => $totalAmount - $expenses,
            'net_profit' => $totalAmount - ($expenses + $salaries),
        ];
        $selectedBranch = $filters['branch_id'] ? Branch::find($filters['branch_id']) : null;
        return view('reports.kasr', compact('branches', 'filters', 'reportData', 'selectedBranch', 'expenses', 'salaries', 'weights', 'calibers'));
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