<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $credentials = [
            'username' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        $user = \App\Models\User::where('username', $credentials['username'])->first();
        if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'اسم المستخدم أو كلمة المرور غير صحيحة']);
        }

        $deviceToken = $request->cookie('device_token');
        $trusted = $deviceToken && \App\Models\Device::where('token', $deviceToken)->where('user_id', $user->id)->exists();

        if ($user->isAdmin() || $trusted) {
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            if ($user->isBranch()) {
                return redirect()->intended(route('sales.create'));
            } elseif ($user->isAccountant()) {
                return redirect()->intended(route('dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        } else {
            // Store pending user id in session for pairing
            $request->session()->put('pending_user_id', $user->id);
            return redirect()->route('pair-device.form')->withInput();
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
