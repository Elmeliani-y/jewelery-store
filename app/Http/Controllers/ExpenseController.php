<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['branch', 'expenseType'])
                       ->orderBy('expense_date', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->expense_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        $expenses = $query->paginate(15);

        $branches = Branch::active()->get();
        $expenseTypes = ExpenseType::active()->get();

        return view('expenses.index', compact('expenses', 'branches', 'expenseTypes'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $user = auth()->user();
        
        // For branch users, pre-select their branch
        if ($user->isBranch()) {
            $branches = Branch::where('id', $user->branch_id)->get();
            $selectedBranchId = $user->branch_id;
        } else {
            $branches = Branch::active()->get();
            $selectedBranchId = null;
        }
        
        $expenseTypes = ExpenseType::active()->get();
        
        return view('expenses.create', compact('branches', 'expenseTypes', 'selectedBranchId'));
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if branch user is trying to access another branch
        $user = auth()->user();
        if ($user->isBranch() && $validated['branch_id'] != $user->branch_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفرع');
        }

        try {
            Expense::create($validated);

            return redirect()->route('expenses.index')
                           ->with('success', 'تم تسجيل المصروف بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'حدث خطأ في تسجيل المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        // Check if branch user is trying to access another branch's expense
        $user = auth()->user();
        if ($user->isBranch() && $expense->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المصروف');
        }

        $expense->load(['branch', 'expenseType']);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        // Check if branch user is trying to access another branch's expense
        $user = auth()->user();
        if ($user->isBranch() && $expense->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بتعديل هذا المصروف');
        }

        $branches = Branch::active()->get();
        $expenseTypes = ExpenseType::active()->get();
        
        return view('expenses.edit', compact('expense', 'branches', 'expenseTypes'));
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense)
    {
        // Check if branch user is trying to access another branch's expense
        $user = auth()->user();
        if ($user->isBranch() && $expense->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بتعديل هذا المصروف');
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $expense->update($validated);

            return redirect()->route('expenses.show', $expense)
                           ->with('success', 'تم تحديث المصروف بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'حدث خطأ في تحديث المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        try {
            $expense->delete();

            return redirect()->route('expenses.index')
                           ->with('success', 'تم حذف المصروف بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف المصروف: ' . $e->getMessage());
        }
    }
}