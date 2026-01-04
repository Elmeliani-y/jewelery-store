<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
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
     * Display a listing of branches.
     */
    public function index()
    {
        $this->validateDeviceOrAbort();
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
        $this->validateDeviceOrAbort();
        $this->enforceDeviceOrAdminOr404(request());
        return view('branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $this->validateDeviceOrAbort();

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

            return redirect()->route('x9y4z1a6.index')
                ->with('success', 'تم إضافة الفرع بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ في إضافة الفرع: '.$e->getMessage());
        }
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $x9y4z1a6)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $branch = $x9y4z1a6; // Alias for clarity
        $branch->loadCount(['employees', 'sales', 'expenses']);

        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $x9y4z1a6)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $branch = $x9y4z1a6; // Alias for clarity
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch.
     */
    public function update(Request $request, Branch $x9y4z1a6)
    {
        $branch = $x9y4z1a6; // Alias for clarity

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,'.$branch->id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        try {
            $branch->update($validated);

            return redirect()->route('x9y4z1a6.show', $branch)
                ->with('success', 'تم تحديث الفرع بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ في تحديث الفرع: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(Branch $x9y4z1a6)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $branch = $x9y4z1a6; // Alias for clarity
        try {
            // Check if branch has employees or sales
            if ($branch->employees()->exists() || $branch->sales()->exists()) {
                return back()->with('error', 'لا يمكن حذف الفرع لأنه يحتوي على موظفين أو مبيعات');
            }

            $branch->delete();

            return redirect()->route('x9y4z1a6.index')
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
        $this->enforceDeviceOrAdminOr404(request());
        try {
            $branch->update(['is_active' => ! $branch->is_active]);

            $status = $branch->is_active ? 'تفعيل' : 'إلغاء تفعيل';

            return back()->with('success', "تم {$status} الفرع بنجاح");

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في تغيير حالة الفرع: '.$e->getMessage());
        }
    }
}
