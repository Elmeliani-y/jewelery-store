<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Caliber;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        // For branch accounts: block listing view entirely
        if ($user && method_exists($user, 'isBranch') && $user->isBranch()) {
            abort(403, 'غير مسموح لحساب الفرع بعرض قائمة المبيعات');
        }

        $query = Sale::with(['branch', 'employee', 'caliber'])
            ->where('is_returned', false)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Category filter removed - categories are now in products JSON
        // if ($request->filled('category_id')) {
        //     $query->where('category_id', $request->category_id);
        // }

        if ($request->filled('caliber_id')) {
            $query->where('caliber_id', $request->caliber_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%'.$request->invoice_number.'%');
        }

        $sales = $query->paginate(15);

        $branches = Branch::active()->get();
        $employees = Employee::active()->get();
        $categories = Category::active()->get();
        $calibers = Caliber::active()->get();

        // Load minimum price setting from database
        $minGramPrice = (float)\App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 2.0));

        return view('sales.index', compact('sales', 'branches', 'employees', 'categories', 'calibers', 'minGramPrice'));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $user = auth()->user();

        // If user is a branch user, pre-select their branch
        if ($user->isBranch()) {
            $branches = Branch::where('id', $user->branch_id)->get();
            $employees = Employee::active()->where('branch_id', $user->branch_id)->get();
            $selectedBranchId = $user->branch_id;
        } else {
            $branches = Branch::active()->get();
            $employees = collect(); // Empty collection, will be loaded via AJAX
            $selectedBranchId = null;
        }

        $categories = Category::with('defaultCaliber')->active()->get();
        $calibers = Caliber::active()->get();

        // Load settings from database
        $settings = [
            'min_invoice_gram_avg' => (float)\App\Models\Setting::get('min_invoice_gram_avg', config('sales.min_invoice_gram_avg', 2.0)),
        ];

        return view('sales.create', compact('branches', 'employees', 'categories', 'calibers', 'selectedBranchId', 'settings'));
    }

    /**
     * Store a newly created sale.
     */
    public function store(Request $request)
    {
        // Build dynamic validation rules based on payment method to avoid network errors when cash selected
        $baseRules = [
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'products' => 'required|array|min:1',
            'products.*.category_id' => 'required|exists:categories,id',
            'products.*.caliber_id' => 'nullable|exists:calibers,id',
            'products.*.weight' => 'required|numeric|min:0.001',
            'products.*.amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,network,mixed,transfer',
            'customer_received' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];

        $pm = $request->input('payment_method');
        if ($pm === 'cash') {
            $baseRules['cash_amount'] = 'required|numeric|min:0';
            $baseRules['network_amount'] = 'nullable|numeric|min:0';
        } elseif ($pm === 'network') {
            $baseRules['cash_amount'] = 'nullable|numeric|min:0';
            $baseRules['network_amount'] = 'required|numeric|min:0';
        } elseif ($pm === 'mixed') {
            $baseRules['cash_amount'] = 'required|numeric|min:0';
            $baseRules['network_amount'] = 'required|numeric|min:0';
        } else {
            // Fallback to nullable to prevent unwanted errors
            $baseRules['cash_amount'] = 'nullable|numeric|min:0';
            $baseRules['network_amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($baseRules, [
            'products.required' => 'يجب إضافة منتج واحد على الأقل.',
            'products.*.category_id.required' => 'الفئة مطلوبة لكل منتج.',
            'products.*.caliber_id.required' => 'العيار مطلوب لكل منتج.',
            'products.*.weight.required' => 'الوزن مطلوب.',
            'products.*.weight.numeric' => 'الوزن يجب أن يكون رقماً.',
            'products.*.amount.required' => 'المبلغ مطلوب.',
            'products.*.amount.numeric' => 'المبلغ يجب أن يكون رقماً.',
            'branch_id.required' => 'الفرع مطلوب.',
            'employee_id.required' => 'الموظف مطلوب.',
            'payment_method.required' => 'طريقة الدفع مطلوبة.',
            'cash_amount.required_if' => 'المبلغ النقدي مطلوب لطريقة الدفع المختارة.',
            'cash_amount.numeric' => 'المبلغ النقدي يجب أن يكون رقماً.',
            'network_amount.required_if' => 'مبلغ الشبكة مطلوب لطريقة الدفع المختارة.',
            'network_amount.numeric' => 'مبلغ الشبكة يجب أن يكون رقماً.',
            'network_reference.required_if' => 'المرجع الشبكي مطلوب لطريقة الدفع المختارة.',
            'network_reference.string' => 'المرجع الشبكي يجب أن يكون نصاً.',
        ], [
            'branch_id' => 'الفرع',
            'employee_id' => 'الموظف',
            'products.*.category_id' => 'الفئة',
            'products.*.caliber_id' => 'العيار',
            'products.*.weight' => 'الوزن',
            'products.*.amount' => 'المبلغ',
            'payment_method' => 'طريقة الدفع',
            'cash_amount' => 'المبلغ النقدي',
            'network_amount' => 'مبلغ الشبكة',
            'network_reference' => 'المرجع الشبكي',
            'notes' => 'الملاحظات',
        ]);

        // Check if branch user is trying to access another branch
        $user = auth()->user();
        if ($user->isBranch() && $validated['branch_id'] != $user->branch_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفرع');
        }

        try {
            DB::beginTransaction();

            // Generate unique invoice number
            $invoiceNumber = Sale::generateInvoiceNumber();

            // Calculate totals across all products
            $totalWeight = 0;
            $totalAmount = 0;
            $totalTax = 0;
            $totalNet = 0;

            $productsData = [];
            foreach ($validated['products'] as $product) {
                $caliberId = $product['caliber_id'] ?? null;
                $caliber = $caliberId ? Caliber::find($caliberId) : null;
                $category = Category::find($product['category_id']);

                // amount entered is total price (with tax), calculate base price = amount / (1 + tax_rate)
                $totalPrice = $product['amount'];
                $taxRate = $caliber ? ($caliber->tax_rate / 100) : 0;
                $baseAmount = $totalPrice / (1 + $taxRate);
                $calculatedTax = $totalPrice - $baseAmount;

                $totalWeight += $product['weight'];
                $totalAmount += $totalPrice;
                $totalTax += $calculatedTax;
                $totalNet += $baseAmount;

                $productsData[] = [
                    'category_id' => $product['category_id'],
                    'category_name' => $category?->name ?? '',
                    'caliber_id' => $caliberId,
                    'caliber_name' => $caliber?->name ?? '',
                    'weight' => $product['weight'],
                    'amount' => $totalPrice,
                    'tax_amount' => $calculatedTax,
                    'net_amount' => $baseAmount,
                ];
            }

            // Create single sale record with all products
            $saleData = [
                'invoice_number' => $invoiceNumber,
                'products' => $productsData,
                'branch_id' => $validated['branch_id'],
                'employee_id' => $validated['employee_id'],
                'weight' => $totalWeight,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'cash_amount' => $validated['cash_amount'] ?? 0,
                'network_amount' => $validated['network_amount'] ?? 0,
                'network_reference' => $validated['network_reference'] ?? null,
                'customer_received' => $request->has('customer_received') ? true : false,
                'notes' => $validated['notes'] ?? null,
                'tax_amount' => $totalTax,
                'net_amount' => $totalNet,
                'is_returned' => $request->has('is_returned'),
                'returned_at' => $request->has('is_returned') ? now() : null,
            ];

            $sale = Sale::create($saleData);

            DB::commit();

            // If the request is AJAX/JSON, return JSON success without redirect
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل المبيعة بنجاح',
                    'data' => [
                        'invoice_number' => $invoiceNumber,
                        'sale_id' => $sale->id,
                        'branch_id' => $validated['branch_id'],
                        'employee_id' => $validated['employee_id'],
                        'payment_method' => $validated['payment_method'],
                        'cash_amount' => $validated['cash_amount'] ?? 0,
                        'network_amount' => $validated['network_amount'] ?? 0,
                    ],
                ]);
            }

            // Redirect branch users to daily sales, others to sale details
            if ($user->isBranch()) {
                return redirect()->route('branch.daily-sales')
                    ->with('success', 'تم تسجيل المبيعة بنجاح');
            }

            return redirect()->route('sales.show', $sale)
                ->with('success', 'تم تسجيل المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ في تسجيل المبيعة: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'حدث خطأ في تسجيل المبيعة: '.$e->getMessage());
        }
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        // Check if branch user is trying to access another branch's sale
        $user = auth()->user();
        if ($user->isBranch() && $sale->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الفاتورة');
        }

        $sale->load(['branch', 'employee']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        // Block branch users from editing sales
        $user = auth()->user();
        if ($user->isBranch()) {
            abort(403, 'غير مسموح لحساب الفرع بتعديل المبيعات');
        }

        if ($sale->is_returned) {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'لا يمكن تعديل فاتورة مسترجعة');
        }

        $branches = Branch::active()->get();
        $employees = Employee::active()->where('branch_id', $sale->branch_id)->get();
        $categories = Category::active()->get();
        $calibers = Caliber::active()->get();

        return view('sales.edit', compact('sale', 'branches', 'employees', 'categories', 'calibers'));
    }

    /**
     * Update the specified sale.
     */
    public function update(Request $request, Sale $sale)
    {
        // Block branch users from updating sales
        $user = auth()->user();
        if ($user->isBranch()) {
            abort(403, 'غير مسموح لحساب الفرع بتعديل المبيعات');
        }

        if ($sale->is_returned) {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'لا يمكن تعديل فاتورة مسترجعة');
        }

        // Dynamic rules for update similar to store
        $updateRules = [
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'products' => 'required|array|min:1',
            'products.*.category_id' => 'required|exists:categories,id',
            'products.*.caliber_id' => 'nullable|exists:calibers,id',
            'products.*.weight' => 'required|numeric|min:0.001',
            'products.*.amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,network,mixed',
            'customer_received' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ];

        $pmUpdate = $request->input('payment_method');
        if ($pmUpdate === 'cash') {
            $updateRules['cash_amount'] = 'required|numeric|min:0.01';
            $updateRules['network_amount'] = 'nullable|numeric|min:0';
        } elseif ($pmUpdate === 'network') {
            $updateRules['cash_amount'] = 'nullable|numeric|min:0';
            $updateRules['network_amount'] = 'required|numeric|min:0.01';
        } elseif ($pmUpdate === 'mixed') {
            $updateRules['cash_amount'] = 'required|numeric|min:0.01';
            $updateRules['network_amount'] = 'required|numeric|min:0.01';
        } else {
            $updateRules['cash_amount'] = 'nullable|numeric|min:0';
            $updateRules['network_amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($updateRules, [
            'branch_id.required' => 'الفرع مطلوب.',
            'employee_id.required' => 'الموظف مطلوب.',
            'category_id.required' => 'الفئة مطلوبة.',
            'caliber_id.required' => 'العيار مطلوب.',
            'weight.required' => 'الوزن مطلوب.',
            'weight.numeric' => 'الوزن يجب أن يكون رقماً.',
            'total_amount.required' => 'المبلغ الإجمالي مطلوب.',
            'total_amount.numeric' => 'المبلغ الإجمالي يجب أن يكون رقماً.',
            'payment_method.required' => 'طريقة الدفع مطلوبة.',
            'cash_amount.required' => 'المبلغ النقدي مطلوب عند اختيار الدفع نقداً أو الدفع المختلط.',
            'cash_amount.min' => 'المبلغ النقدي يجب أن يكون أكبر من صفر.',
            'cash_amount.numeric' => 'المبلغ النقدي يجب أن يكون رقماً.',
            'network_amount.required' => 'مبلغ الشبكة مطلوب عند اختيار الدفع بالشبكة أو الدفع المختلط.',
            'network_amount.min' => 'مبلغ الشبكة يجب أن يكون أكبر من صفر.',
            'network_amount.numeric' => 'مبلغ الشبكة يجب أن يكون رقماً.',
            'network_reference.required_if' => 'المرجع الشبكي مطلوب لطريقة الدفع المختارة.',
            'network_reference.string' => 'المرجع الشبكي يجب أن يكون نصاً.',
        ], [
            'branch_id' => 'الفرع',
            'employee_id' => 'الموظف',
            'category_id' => 'الفئة',
            'caliber_id' => 'العيار',
            'weight' => 'الوزن',
            'total_amount' => 'المبلغ الإجمالي',
            'payment_method' => 'طريقة الدفع',
            'cash_amount' => 'المبلغ النقدي',
            'network_amount' => 'مبلغ الشبكة',
            'network_reference' => 'المرجع الشبكي',
            'notes' => 'الملاحظات',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate totals from products
            $totalWeight = 0;
            $totalAmount = 0;
            $totalTax = 0;
            $totalNet = 0;

            $productsData = [];
            foreach ($validated['products'] as $product) {
                $caliberId = $product['caliber_id'] ?? null;
                $caliber = $caliberId ? Caliber::find($caliberId) : null;
                $category = Category::find($product['category_id']);

                // amount entered is total price (with tax), calculate base price = amount / (1 + tax_rate)
                $totalPrice = $product['amount'];
                $taxRate = $caliber ? ($caliber->tax_rate / 100) : 0;
                $baseAmount = $totalPrice / (1 + $taxRate);
                $calculatedTax = $totalPrice - $baseAmount;

                $totalWeight += $product['weight'];
                $totalAmount += $totalPrice;
                $totalTax += $calculatedTax;
                $totalNet += $baseAmount;

                $productsData[] = [
                    'category_id' => $product['category_id'],
                    'category_name' => $category?->name ?? '',
                    'caliber_id' => $caliberId,
                    'caliber_name' => $caliber?->name ?? '',
                    'weight' => $product['weight'],
                    'amount' => $totalPrice,
                    'tax_amount' => $calculatedTax,
                    'net_amount' => $baseAmount,
                ];
            }

            $sale->update([
                'products' => $productsData,
                'branch_id' => $validated['branch_id'],
                'employee_id' => $validated['employee_id'],
                'weight' => $totalWeight,
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'net_amount' => $totalNet,
                'payment_method' => $validated['payment_method'],
                'cash_amount' => $validated['cash_amount'] ?? 0,
                'network_amount' => $validated['network_amount'] ?? 0,
                'network_reference' => $validated['network_reference'] ?? null,
                'customer_received' => $request->has('customer_received') ? true : false,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'تم تحديث المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'حدث خطأ في تحديث المبيعة: '.$e->getMessage());
        }
    }

    /**
     * Return the specified sale.
     */
    public function returnSale(Sale $sale)
    {
        if ($sale->is_returned) {
            return redirect()->route('sales.index')
                ->with('error', 'هذه الفاتورة مسترجعة بالفعل');
        }

        try {
            $sale->returnSale();

            return redirect()->route('sales.index')
                ->with('success', 'تم استرجاع الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في استرجاع الفاتورة: '.$e->getMessage());
        }
    }

    /**
     * Restore a returned sale as a normal sale.
     */
    public function unreturnSale(Sale $sale)
    {
        if (!$sale->is_returned) {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'هذه الفاتورة ليست مرتجعاً');
        }
        $sale->update([
            'is_returned' => false,
            'returned_at' => null,
        ]);
        return redirect()->route('sales.index')->with('success', 'تمت إعادة الفاتورة إلى قائمة المبيعات بنجاح');
    }

    /**
     * Get employees by branch (AJAX).
     */
    public function getEmployeesByBranch(Request $request)
    {
        $employees = Employee::active()
            ->where('branch_id', $request->branch_id)
            ->get(['id', 'name', 'is_snap']);

        return response()->json($employees);
    }

    /**
     * Search sales by invoice number (AJAX).
     */
    public function searchByInvoice(Request $request)
    {
        $query = $request->get('q');

        $sales = Sale::with(['branch', 'employee', 'caliber'])
            ->where('invoice_number', 'like', '%'.$query.'%')
            ->limit(10)
            ->get();

        return response()->json($sales);
    }

    /**
     * Remove the specified sale.
     */
    public function destroy(Sale $sale)
    {
        // Block all branch users from deleting sales
        $user = auth()->user();
        if ($user->isBranch()) {
            abort(403, 'غير مسموح لحساب الفرع بحذف المبيعات');
        }

        try {
            $sale->delete();

            return redirect()->route('sales.index')
                ->with('success', 'تم حذف المبيعة بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف المبيعة: '.$e->getMessage());
        }
    }

    /**
     * Display daily sales for the current branch
     */
    public function dailySales(Request $request)
    {
        $user = auth()->user();
        
        // Only branch users can access
        if (!$user->isBranch()) {
            abort(403, 'هذه الصفحة مخصصة لحسابات الفروع فقط');
        }

        // Get today's sales for the user's branch
        $query = Sale::with(['employee', 'caliber'])
            ->where('branch_id', $user->branch_id)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc');

        // Get all employees for filter
        $employees = Employee::where('branch_id', $user->branch_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Apply employee filter if provided
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Apply customer received filter if provided
        if ($request->filled('customer_received')) {
            $customerReceived = $request->customer_received === 'yes' ? true : false;
            $query->where('customer_received', $customerReceived);
        }

        $sales = $query->get();

        // Calculate totals
        $totalWeight = $sales->reduce(function($carry, $sale) {
            if (is_array($sale->products)) {
                foreach ($sale->products as $product) {
                    $carry += isset($product['weight']) ? (float)$product['weight'] : 0;
                }
            }
            return $carry;
        }, 0);
        $totalAmount = $sales->sum('total_amount');
        $averageRate = $totalWeight > 0 ? $totalAmount / $totalWeight : 0;

        // Calculate payment type totals
        // Cash only includes: pure cash payments + cash portion of mixed payments
        $cashOnlyTotal = $sales->where('payment_method', 'cash')->sum('total_amount') 
                       + $sales->where('payment_method', 'mixed')->sum('cash_amount');
        
        // Network only includes: pure network payments + network portion of mixed payments
        $networkOnlyTotal = $sales->where('payment_method', 'network')->sum('total_amount') 
                          + $sales->where('payment_method', 'mixed')->sum('network_amount');
        
        $mixedTotal = $sales->where('payment_method', 'mixed')->sum('total_amount');

        return view('sales.daily', compact('sales', 'employees', 'totalWeight', 'totalAmount', 'averageRate', 'cashOnlyTotal', 'networkOnlyTotal', 'mixedTotal'));
    }

    /**
     * Show only returned sales (مرتجع) in the returns dashboard.
     */
    public function returns(Request $request)
    {
        $query = Sale::with(['branch', 'employee', 'caliber'])
            ->where('is_returned', true)
            ->orderBy('returned_at', 'desc');

        // Apply invoice_number filter if present
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%'.$request->invoice_number.'%');
        }

        $returns = $query->paginate(15);
        return view('sales.returns', compact('returns'));
    }
}
