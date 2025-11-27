<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Category;
use App\Models\Caliber;
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

        $query = Sale::with(['branch', 'employee', 'category', 'caliber'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

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
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        $sales = $query->paginate(15);

        $branches = Branch::active()->get();
        $employees = Employee::active()->get();
        $categories = Category::active()->get();
        $calibers = Caliber::active()->get();

        return view('sales.index', compact('sales', 'branches', 'employees', 'categories', 'calibers'));
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
        
        $categories = Category::active()->get();
        $calibers = Caliber::active()->get();
        
        return view('sales.create', compact('branches', 'employees', 'categories', 'calibers', 'selectedBranchId'));
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
            'products.*.caliber_id' => 'required|exists:calibers,id',
            'products.*.weight' => 'required|numeric|min:0.001',
            'products.*.amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,network,mixed',
            'notes' => 'nullable|string|max:1000',
        ];

        $pm = $request->input('payment_method');
        if ($pm === 'cash') {
            $baseRules['cash_amount'] = 'required|numeric|min:0';
            $baseRules['network_amount'] = 'nullable|numeric|min:0';
            $baseRules['network_reference'] = 'nullable|string|max:255';
        } elseif ($pm === 'network') {
            $baseRules['cash_amount'] = 'nullable|numeric|min:0';
            $baseRules['network_amount'] = 'required|numeric|min:0';
            $baseRules['network_reference'] = 'required|string|max:255';
        } elseif ($pm === 'mixed') {
            $baseRules['cash_amount'] = 'required|numeric|min:0';
            $baseRules['network_amount'] = 'required|numeric|min:0';
            $baseRules['network_reference'] = 'required|string|max:255';
        } else {
            // Fallback to nullable to prevent unwanted errors
            $baseRules['cash_amount'] = 'nullable|numeric|min:0';
            $baseRules['network_amount'] = 'nullable|numeric|min:0';
            $baseRules['network_reference'] = 'nullable|string|max:255';
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

            // Generate invoice number for all products
            $invoiceNumber = Sale::generateInvoiceNumber();
            
            $firstSale = null;
            
            // Create a sale record for each product
            foreach ($validated['products'] as $product) {
                // حساب الضريبة والمبلغ الصافي مسبقاً لتجنب خطأ عدم وجود قيمة net_amount
                $caliber = Caliber::find($product['caliber_id']);
                $calculatedTax = $caliber ? $caliber->calculateTax($product['amount']) : 0;
                $calculatedNet = $product['amount'] - $calculatedTax;
                $saleData = [
                    'invoice_number' => $invoiceNumber,
                    'branch_id' => $validated['branch_id'],
                    'employee_id' => $validated['employee_id'],
                    'category_id' => $product['category_id'],
                    'caliber_id' => $product['caliber_id'],
                    'weight' => $product['weight'],
                    'total_amount' => $product['amount'],
                    'payment_method' => $validated['payment_method'],
                    'cash_amount' => $validated['cash_amount'] ?? 0,
                    'network_amount' => $validated['network_amount'] ?? 0,
                    'network_reference' => $validated['network_reference'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'tax_amount' => $calculatedTax,
                    'net_amount' => $calculatedNet,
                ];

                $sale = Sale::create($saleData);
                
                // Calculate tax and net amounts
                $sale->calculateAmounts();
                $sale->save();
                
                // Keep reference to first sale for redirect
                if (!$firstSale) {
                    $firstSale = $sale;
                }
            }

            DB::commit();

            // If the request is AJAX/JSON, return JSON success without redirect
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل المبيعة بنجاح',
                    'data' => [
                        'invoice_number' => $invoiceNumber,
                        'first_sale_id' => $firstSale?->id,
                        'branch_id' => $validated['branch_id'],
                        'employee_id' => $validated['employee_id'],
                        'payment_method' => $validated['payment_method'],
                        'cash_amount' => $validated['cash_amount'] ?? 0,
                        'network_amount' => $validated['network_amount'] ?? 0,
                        'network_reference' => $validated['network_reference'] ?? null,
                    ],
                ]);
            }

            return redirect()->route('sales.show', $firstSale)
                           ->with('success', 'تم تسجيل المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ في تسجيل المبيعة: ' . $e->getMessage(),
                ], 500);
            }
            return back()->withInput()
                        ->with('error', 'حدث خطأ في تسجيل المبيعة: ' . $e->getMessage());
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

        $sale->load(['branch', 'employee', 'category', 'caliber']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        // Check if branch user is trying to access another branch's sale
        $user = auth()->user();
        if ($user->isBranch() && $sale->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الفاتورة');
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
        // Check if branch user is trying to access another branch's sale
        $user = auth()->user();
        if ($user->isBranch() && $sale->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الفاتورة');
        }

        if ($sale->is_returned) {
            return redirect()->route('sales.show', $sale)
                           ->with('error', 'لا يمكن تعديل فاتورة مسترجعة');
        }

        // Dynamic rules for update similar to store
        $updateRules = [
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:categories,id',
            'caliber_id' => 'required|exists:calibers,id',
            'weight' => 'required|numeric|min:0.001',
            'total_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,network,mixed',
            'notes' => 'nullable|string|max:1000',
        ];

        $pmUpdate = $request->input('payment_method');
        if ($pmUpdate === 'cash') {
            $updateRules['cash_amount'] = 'required|numeric|min:0';
            $updateRules['network_amount'] = 'nullable|numeric|min:0';
            $updateRules['network_reference'] = 'nullable|string|max:255';
        } elseif ($pmUpdate === 'network') {
            $updateRules['cash_amount'] = 'nullable|numeric|min:0';
            $updateRules['network_amount'] = 'required|numeric|min:0';
            $updateRules['network_reference'] = 'required|string|max:255';
        } elseif ($pmUpdate === 'mixed') {
            $updateRules['cash_amount'] = 'required|numeric|min:0';
            $updateRules['network_amount'] = 'required|numeric|min:0';
            $updateRules['network_reference'] = 'required|string|max:255';
        } else {
            $updateRules['cash_amount'] = 'nullable|numeric|min:0';
            $updateRules['network_amount'] = 'nullable|numeric|min:0';
            $updateRules['network_reference'] = 'nullable|string|max:255';
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
            'cash_amount.required_if' => 'المبلغ النقدي مطلوب لطريقة الدفع المختارة.',
            'cash_amount.numeric' => 'المبلغ النقدي يجب أن يكون رقماً.',
            'network_amount.required_if' => 'مبلغ الشبكة مطلوب لطريقة الدفع المختارة.',
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

            $sale->update($validated);

            // Recalculate tax and net amounts
            $sale->calculateAmounts();
            $sale->save();

            DB::commit();

            return redirect()->route('sales.show', $sale)
                           ->with('success', 'تم تحديث المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'حدث خطأ في تحديث المبيعة: ' . $e->getMessage());
        }
    }

    /**
     * Return the specified sale.
     */
    public function returnSale(Sale $sale)
    {
        if ($sale->is_returned) {
            return redirect()->route('sales.show', $sale)
                           ->with('error', 'هذه الفاتورة مسترجعة بالفعل');
        }

        try {
            $sale->returnSale();

            return redirect()->route('sales.show', $sale)
                           ->with('success', 'تم استرجاع الفاتورة بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في استرجاع الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Get employees by branch (AJAX).
     */
    public function getEmployeesByBranch(Request $request)
    {
        $employees = Employee::active()
                            ->where('branch_id', $request->branch_id)
                            ->get(['id', 'name']);

        return response()->json($employees);
    }

    /**
     * Search sales by invoice number (AJAX).
     */
    public function searchByInvoice(Request $request)
    {
        $query = $request->get('q');
        
        $sales = Sale::with(['branch', 'employee', 'category', 'caliber'])
                    ->where('invoice_number', 'like', '%' . $query . '%')
                    ->limit(10)
                    ->get();

        return response()->json($sales);
    }

    /**
     * Remove the specified sale.
     */
    public function destroy(Sale $sale)
    {
        // Allow only same-branch deletion for branch users
        $user = auth()->user();
        if ($user->isBranch() && $sale->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بحذف هذه الفاتورة');
        }

        try {
            $sale->delete();

            return redirect()->route('sales.index')
                           ->with('success', 'تم حذف المبيعة بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف المبيعة: ' . $e->getMessage());
        }
    }
}