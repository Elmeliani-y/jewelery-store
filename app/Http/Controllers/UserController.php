<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
     * Display a listing of users.
     */
    public function index()
    {
        $this->enforceDeviceToken(request());
        $users = User::with('branch')->orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->enforceDeviceToken(request());
        $branches = Branch::where('is_active', true)->get();
        return view('users.create', compact('branches'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->enforceDeviceToken($request);
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
                        ->with('error', 'حدث خطأ في إضافة المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->enforceDeviceToken(request());
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
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,accountant,branch',
            'branch_id' => $request->role === 'branch' ? 'required|exists:branches,id' : 'nullable|exists:branches,id',
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('users.show', $user)
                           ->with('success', 'تم تحديث المستخدم بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'حدث خطأ في تحديث المستخدم: ' . $e->getMessage());
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

            $user->delete();

            return redirect()->route('users.index')
                           ->with('success', 'تم حذف المستخدم بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في حذف المستخدم: ' . $e->getMessage());
        }
    }
}
