<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

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
     * Display a listing of users.
     */
    public function index()
    {
        $this->validateDeviceOrAbort();
        $users = User::with('branch')->orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->enforceDeviceOrAdminOr404(request());
        $branches = Branch::where('is_active', true)->get();

        return view('users.create', compact('branches'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->enforceDeviceOrAdminOr404($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,accountant,branch',
            'branch_id' => $request->role === 'branch' ? 'required|exists:branches,id' : 'nullable|exists:branches,id',
        ]);

        try {
            $validated['password'] = bcrypt($validated['password']);
            User::create($validated);

            return redirect()->route('users.index')
                ->with('success', 'تم إضافة المستخدم بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ في إضافة المستخدم: '.$e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->enforceDeviceOrAdminOr404(request());
        $user->load('branch');

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->enforceDeviceToken(request());
        $branches = Branch::where('is_active', true)->get();

        return view('users.edit', compact('user', 'branches'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->enforceDeviceToken($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,accountant,branch',
            'branch_id' => $request->role === 'branch' ? 'required|exists:branches,id' : 'nullable|exists:branches,id',
        ]);

        try {
            if (! empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('users.show', $user)
                ->with('success', 'تم تحديث المستخدم بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ في تحديث المستخدم: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->enforceDeviceToken(request());
        try {
            // Prevent deleting current logged-in user
            if ($user->id === auth()->id()) {
                return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
            }

            // Delete all devices for this user
            \App\Models\Device::where('user_id', $user->id)->delete();

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'تم حذف المستخدم وجميع أجهزته بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف المستخدم: '.$e->getMessage());
        }
    }
}
