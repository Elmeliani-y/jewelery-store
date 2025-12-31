<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Removed device validation from constructor; now enforced at the start of every action.
    private function validateDeviceOrAbort()
    {
        $token = request()->cookie('device_token');
        if ($token) {
            $device = \App\Models\Device::where('token', $token)->first();
            if (! $device || ! $device->active || ! $device->user_id || ! \App\Models\User::where('id', $device->user_id)->exists()) {
                \Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                \Cookie::queue(\Cookie::forget('device_token'));
                abort(404);
            }
        }
    }
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        // Block listing for branch accounts entirely
        if ($user && method_exists($user, 'isBranch') && $user->isBranch()) {
            abort(403, 'غير مسموح لحساب الفرع بعرض قائمة المصروفات');
        }

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
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        // Filter by expense id if present
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        $expenses = $query->paginate(15);
        // Compute total (ijmali) for filtered expenses (all pages)
        $totalExpenses = (clone $query)->sum('amount');

        $branches = Branch::active()->get();
        $expenseTypes = ExpenseType::active()->get();

        return view('expenses.index', compact('expenses', 'branches', 'expenseTypes', 'totalExpenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $this->enforceDeviceOrAdminOr404(request());
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
        $this->enforceDeviceToken($request);
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'branch_id.required' => 'الفرع مطلوب.',
            'branch_id.exists' => 'الفرع المحدد غير موجود.',
            'expense_type_id.required' => 'نوع المصروف مطلوب.',
            'expense_type_id.exists' => 'نوع المصروف المحدد غير موجود.',
            'description.required' => 'الوصف مطلوب.',
            'description.string' => 'الوصف يجب أن يكون نصاً.',
            'description.max' => 'الوصف يجب ألا يتجاوز :max حرفاً.',
            'amount.required' => 'المبلغ مطلوب.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً.',
            'amount.min' => 'المبلغ يجب أن يكون على الأقل :min.',
            'expense_date.required' => 'تاريخ المصروف مطلوب.',
        ]);

        // Check if branch user is trying to access another branch
        $user = auth()->user();
        if ($user->isBranch() && $validated['branch_id'] != $user->branch_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفرع');
        }

        try {
            $expense = Expense::create($validated);

            // If the request is AJAX/JSON, return a JSON response instead of redirecting
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل المصروف بنجاح',
                    'data' => [
                        'id' => $expense->id,
                        'branch_id' => $expense->branch_id,
                        'expense_type_id' => $expense->expense_type_id,
                        'description' => $expense->description,
                        'amount' => $expense->amount,
                        'expense_date' => $expense->expense_date,
                        'notes' => $expense->notes,
                    ],
                ]);
            }

            // Redirect branch users to daily expenses, others to expense list
            if ($user->isBranch()) {
                return redirect()->route('branch.daily-expenses')
                    ->with('success', 'تم تسجيل المصروف بنجاح');
            }

            return redirect()->route('expenses.index')
                ->with('success', 'تم تسجيل المصروف بنجاح');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ في تسجيل المصروف: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'حدث خطأ في تسجيل المصروف: '.$e->getMessage());
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $this->enforceDeviceToken(request());
        // Check if branch user is trying to access another branch's expense
        $user = auth()->user();
        if ($user->isBranch() && $expense->branch_id != $user->branch_id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المصروف');
        }

        $expense->load(['branch', 'expenseType']);

        return view('expenses.show', compact('expense'));
    }


    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense)
    {
        $this->enforceDeviceToken(request());
        $user = auth()->user();
        // Block all deletes for branch accounts
        if ($user->isBranch()) {
            abort(403, 'غير مسموح لحساب الفرع بحذف المصروفات');
        }
        try {
            $expense->delete();

            return redirect()->route('expenses.index')
                ->with('success', 'تم حذف المصروف بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف المصروف: '.$e->getMessage());
        }
    }

    /**
     * Display daily expenses for the current branch
     */
    public function dailyExpenses(Request $request)
    {
        $this->enforceDeviceToken($request);
        $user = auth()->user();
        
        // Only branch users can access this page
        if (!$user->isBranch()) {
            abort(403, 'هذه الصفحة مخصصة لحسابات الفروع فقط');
        }

        // Get today's expenses for the user's branch
        $query = Expense::with(['expenseType'])
            ->where('branch_id', $user->branch_id)
            ->whereDate('expense_date', today())
            ->orderBy('expense_date', 'desc');

        // Get all expense types for filter
        $expenseTypes = ExpenseType::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Optionally filter by expense type
        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->expense_type_id);
        }

        $expenses = $query->get();
        $totalExpenses = $expenses->sum('amount');

        return view('expenses.daily', compact('expenses', 'expenseTypes', 'totalExpenses'));
    }
}