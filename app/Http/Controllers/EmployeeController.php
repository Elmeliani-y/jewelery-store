<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with('branch')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $employees = $query->paginate(15);
        $branches = Branch::active()->get();

        return view('employees.index', compact('employees', 'branches'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $branches = Branch::active()->get();
        return view('employees.create', compact('branches'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:employees,email',
            'salary' => 'required|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        try {
            Employee::create($validated);

            return redirect()->route('employees.index')
                           ->with('success', 'تم إضافة الموظف بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'حدث خطأ في إضافة الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load('branch');
        
        // Get employee sales statistics
        $salesStats = [
            'total_sales' => $employee->sales()->notReturned()->sum('total_amount'),
            'total_weight' => $employee->sales()->notReturned()->get()->reduce(function($carry, $sale) {
                if (is_array($sale->products)) {
                    foreach ($sale->products as $product) {
                        $carry += isset($product['weight']) ? (float)$product['weight'] : 0;
                    }
                }
                return $carry;
            }, 0),
            'sales_count' => $employee->sales()->notReturned()->count(),
            'monthly_sales' => $employee->totalSalesInPeriod(now()->startOfMonth(), now()->endOfMonth()),
        ];

        return view('employees.show', compact('employee', 'salesStats'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $branches = Branch::active()->get();
        return view('employees.edit', compact('employee', 'branches'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id,
            'salary' => 'required|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        try {
            $employee->update($validated);

            return redirect()->route('employees.show', $employee)
                           ->with('success', 'تم تحديث الموظف بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'حدث خطأ في تحديث الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        try {
            // Check if employee has sales
            if ($employee->sales()->exists()) {
                return back()->with('error', 'لا يمكن حذف الموظف لأنه يحتوي على مبيعات');
            }

            $employee->delete();

            return redirect()->route('employees.index')
                           ->with('success', 'تم حذف الموظف بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Toggle employee status.
     */
    public function toggleStatus(Employee $employee)
    {
        try {
            $employee->update(['is_active' => !$employee->is_active]);

            $status = $employee->is_active ? 'تفعيل' : 'إلغاء تفعيل';
            return back()->with('success', "تم {$status} الموظف بنجاح");

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في تغيير حالة الموظف: ' . $e->getMessage());
        }
    }
}