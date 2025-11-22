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
        
        $query = Sale::with(['branch', 'employee', 'category', 'caliber'])
                    ->orderBy('created_at', 'desc');

        // Filter by branch for branch users
        if ($user->isBranch()) {
            $query->where('branch_id', $user->branch_id);
        }

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
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'products' => 'required|array|min:1',
            'products.*.category_id' => 'required|exists:categories,id',
            'products.*.caliber_id' => 'required|exists:calibers,id',
            'products.*.weight' => 'required|numeric|min:0.001',
            'products.*.amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,network,mixed',
            'cash_amount' => 'required_if:payment_method,cash,mixed|numeric|min:0',
            'network_amount' => 'required_if:payment_method,network,mixed|numeric|min:0',
            'network_reference' => 'required_if:payment_method,network,mixed|string|max:255',
            'notes' => 'nullable|string|max:1000',
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

            return redirect()->route('sales.show', $firstSale)
                           ->with('success', 'تم تسجيل المبيعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
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

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:categories,id',
            'caliber_id' => 'required|exists:calibers,id',
            'weight' => 'required|numeric|min:0.001',
            'total_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,network,mixed',
            'cash_amount' => 'required_if:payment_method,cash,mixed|numeric|min:0',
            'network_amount' => 'required_if:payment_method,network,mixed|numeric|min:0',
            'network_reference' => 'required_if:payment_method,network,mixed|string|max:255',
            'notes' => 'nullable|string|max:1000',
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
}