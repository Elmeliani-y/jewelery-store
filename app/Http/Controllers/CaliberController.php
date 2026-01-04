<?php

namespace App\Http\Controllers;

use App\Models\Caliber;
use Illuminate\Http\Request;

class CaliberController extends Controller
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
     * Display a listing of calibers.
     */
    public function index()
    {
        $this->validateDeviceOrAbort();
        $calibers = Caliber::orderBy('name')->get();
        return view('calibers.index', compact('calibers'));
    }

    /**
     * Show the form for creating a new caliber.
     */
    public function create()
    {
        $this->validateDeviceOrAbort();
        $this->enforceDeviceOrAdminOr404(request());
        return view('calibers.create');
    }

    /**
     * Store a newly created caliber.
     */
    public function store(Request $request)
    {
        $this->validateDeviceOrAbort();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calibers,name',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Default is_active to true if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Caliber::create($validated);

        return redirect()->route('n6o1p4q9.index')
                       ->with('success', 'تم إضافة العيار بنجاح');
    }

    /**
     * Show the form for editing the specified caliber.
     */
    public function edit(Caliber $n6o1p4q9)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $caliber = $n6o1p4q9; // Alias for clarity
        return view('calibers.edit', compact('caliber'));
    }

    /**
     * Update the specified caliber.
     */
    public function update(Request $request, Caliber $n6o1p4q9)
    {
        $caliber = $n6o1p4q9; // Alias for clarity

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calibers,name,' . $caliber->id,
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        // Default is_active to false if not provided (checkbox not checked)
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $caliber->update($validated);

        return redirect()->route('n6o1p4q9.index')
                       ->with('success', 'تم تحديث العيار بنجاح');
    }

    /**
     * Toggle caliber status.
     */
    public function toggleStatus(Caliber $caliber)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $caliber->update([
            'is_active' => !$caliber->is_active
        ]);

        $status = $caliber->is_active ? 'مفعل' : 'معطل';
        return back()->with('success', "تم تغيير حالة العيار إلى {$status}");
    }

    /**
     * Remove the specified caliber.
     */
    public function destroy(Caliber $n6o1p4q9)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $caliber = $n6o1p4q9; // Alias for clarity
        // Check if caliber is used in any sales
        if ($caliber->sales()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف العيار لأنه مستخدم في المبيعات');
        }

        $caliber->delete();

        return redirect()->route('n6o1p4q9.index')
                       ->with('success', 'تم حذف العيار بنجاح');
    }
}
