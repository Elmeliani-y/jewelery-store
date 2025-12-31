<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    protected function validateDeviceOrAbort()
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
     * Display a listing of expense types.
     */
    public function index()
    {
        $this->validateDeviceOrAbort();
        $expenseTypes = ExpenseType::withCount('expenses')->orderBy('name')->paginate(15);
        return view('expense_types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new expense type.
     */
    public function create()
    {
        $this->validateDeviceOrAbort();
        return view('expense_types.create');
    }

    /**
     * Store a newly created expense type.
     */
    public function store(Request $request)
    {
        $this->validateDeviceOrAbort();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name',
        ], [
            'name.required' => 'اسم نوع المصروف مطلوب.',
            'name.unique' => 'هذا نوع المصروف موجود بالفعل.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $expenseType = ExpenseType::create($validated);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $expenseType->id,
                'name' => $expenseType->name,
                'is_active' => $expenseType->is_active
            ]);
        }

        return redirect()->route('expense-types.index')
                       ->with('success', 'تم إضافة نوع المصروف بنجاح');
    }

    /**
     * Show the form for editing the specified expense type.
     */
    public function edit(ExpenseType $expenseType)
    {
        $this->validateDeviceOrAbort();
        return view('expense_types.edit', compact('expenseType'));
    }

    /**
     * Update the specified expense type.
     */
    public function update(Request $request, ExpenseType $expenseType)
    {
        $this->validateDeviceOrAbort();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name,' . $expenseType->id,
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم نوع المصروف مطلوب.',
            'name.unique' => 'هذا نوع المصروف موجود بالفعل.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $expenseType->update($validated);

        return redirect()->route('expense-types.index')
                       ->with('success', 'تم تحديث نوع المصروف بنجاح');
    }

    /**
     * Remove the specified expense type.
     */
    public function destroy(ExpenseType $expenseType)
    {
        $this->validateDeviceOrAbort();
        $expenseType->delete();
        return redirect()->route('expense-types.index')
                       ->with('success', 'تم حذف نوع المصروف بنجاح');
    }
}
