<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Check if the current device is trusted (token exists in DB for user).
     * Redirects to pairing page if not trusted.
     */
    private function enforceDeviceToken(Request $request)
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            $deviceToken = $request->cookie('device_token');
            if (!$deviceToken || !\App\Models\Device::where('token', $deviceToken)->where('user_id', $user->id)->exists()) {
                return redirect()->route('pair-device.form')->send();
            }
        }
    }
    /**
     * Display a listing of branches.
     */
    public function index()
    {
        $this->enforceDeviceToken(request());
        $branches = Branch::withCount(['employees', 'sales', 'expenses'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Ensure every branch has a Snap employee
        foreach ($branches as $branch) {
            $hasSnapEmployee = $branch->employees()->where('is_snap', true)->exists();
            if (!$hasSnapEmployee) {
                \App\Models\Employee::create([
                    'name' => 'Snap (' . $branch->name . ')',
                    'phone' => null,
                    'email' => null,
                    'salary' => 0,
                    'branch_id' => $branch->id,
                    'is_active' => true,
                    'is_snap' => true,
                ]);
            }
        }

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        $this->enforceDeviceToken(request());
        return view('branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $this->enforceDeviceToken($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $branch = Branch::create($validated);

            // Create Snap employee for this branch
            \App\Models\Employee::create([
                'name' => 'Snap (' . $branch->name . ')',
                'phone' => null,
                'email' => null,
                'salary' => 0,
                'branch_id' => $branch->id,
                'is_active' => true,
                'is_snap' => true,
            ]);

                // Create SnapAccount for this branch, name = branch name + ' Snap'
                \App\Models\SnapAccount::create([
                    'branch_id' => $branch->id,
                    'type' => 'snap',
                    'name' => $branch->name . ' Snap',
                    'number' => null, // or set as needed
                ]);

            return redirect()->route('branches.index')
                ->with('success', 'تم إضافة الفرع بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ في إضافة الفرع: '.$e->getMessage());
        }
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch)
    {
        $this->enforceDeviceToken(request());
        $branch->loadCount(['employees', 'sales', 'expenses']);

        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        $this->enforceDeviceToken(request());
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $this->enforceDeviceToken($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,'.$branch->id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $branch->update($validated);

            return redirect()->route('branches.show', $branch)
                ->with('success', 'تم تحديث الفرع بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ في تحديث الفرع: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(Branch $branch)
    {
        $this->enforceDeviceToken(request());
        try {
            // Check if branch has employees or sales
            if ($branch->employees()->exists() || $branch->sales()->exists()) {
                return back()->with('error', 'لا يمكن حذف الفرع لأنه يحتوي على موظفين أو مبيعات');
            }

            $branch->delete();

            return redirect()->route('branches.index')
                ->with('success', 'تم حذف الفرع بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف الفرع: '.$e->getMessage());
        }
    }

    /**
     * Toggle branch status.
     */
    public function toggleStatus(Branch $branch)
    {
        $this->enforceDeviceToken(request());
        try {
            $branch->update(['is_active' => ! $branch->is_active]);

            $status = $branch->is_active ? 'تفعيل' : 'إلغاء تفعيل';

            return back()->with('success', "تم {$status} الفرع بنجاح");

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في تغيير حالة الفرع: '.$e->getMessage());
        }
    }
}
