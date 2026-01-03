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
    public function create(Request $request)
    {
        $hasDevice = $request->cookie('device_token');
        $hasAdminSecret = $request->session()->get('admin_secret_used');
        $hasUserLink = $request->session()->get('user_link_token_used');
        if (!$hasDevice && !$hasAdminSecret && !$hasUserLink) {
            // Show blank page for unauthorized access
            return response()->view('landing');
        }
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
            // Track failed login attempts (skip for admin users)
            $ipAddress = $request->ip();
            
            // Only block non-admin attempts
            if (!$user || !$user->isAdmin()) {
                \App\Models\BlockedIp::recordFailedAttempt($ipAddress);
                
                // Check if IP should be blocked after 3 failed attempts
                if (\App\Models\BlockedIp::isBlocked($ipAddress)) {
                    return response()->view('errors.403', [
                        'message' => 'Your IP address has been blocked due to multiple failed login attempts.'
                    ], 403);
                }
            }
            
            return back()->withErrors(['email' => 'اسم المستخدم أو كلمة المرور غير صحيحة']);
        }

        // Only allow login session if device_token or admin_secret_used is present
        $hasDevice = $request->cookie('device_token');
        $hasAdminSecret = $request->session()->get('admin_secret_used');
        $hasUserLink = $request->session()->get('user_link_token_used');
        if (!$hasDevice && !$hasAdminSecret && !$hasUserLink) {
            return redirect()->route('login')->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
        }
        // If using admin link, only allow admin users
        if ($hasAdminSecret && !$user->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            // Clear device_token cookie and abort with 404 to break redirect loop
            \Cookie::queue(\Cookie::forget('device_token'));
            abort(404, 'فقط الأدمن يمكنه الدخول من رابط الأدمن.');
        }

        // Reset failed attempts on successful login
        \App\Models\BlockedIp::resetAttempts($request->ip());
        
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();
        // If login was via user link, re-set the device_token cookie to ensure it persists
        $userLinkToken = $request->session()->get('user_link_token_used');
        $deviceToken = $request->cookie('device_token');
        $device = null;
        if ($userLinkToken) {
            $device = \App\Models\Device::where('token', $userLinkToken)->first();
            if ($device) {
                \Cookie::queue('device_token', $device->token, 525600);
            }
        } elseif ($deviceToken) {
            $device = \App\Models\Device::where('token', $deviceToken)->first();
        }
        if ($device) {
            $device->last_login_at = now();
            $device->save();
        }
        // Ensure admin_secret_used session key is always present after admin login
        if ($request->session()->get('admin_secret_used') && $user->isAdmin()) {
            $request->session()->put('admin_secret_used', true);
            // Force set device_token for admin, always for current host
            $device = \App\Models\Device::firstOrCreate(
                ['name' => 'admin'],
                [
                    'token' => 'admin-static',
                    'user_id' => $user->id,
                    'last_login_at' => now(),
                ]
            );
            // Always set cookie for current host, no domain restriction
            // Set device_token cookie to expire in 1 year from now
            $cookie = cookie('device_token', $device->token, 525600, '/', null, false, true, false, 'Lax');
            \Cookie::queue($cookie);
        }
        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->intended(route('settings.devices'));
        }
        // For branch users, redirect to sales page (daily sales)
        if ($user->isBranch()) {
            return redirect()->intended(route('sales.create'));
        }
        // For accountant users, redirect to dashboard
        return redirect()->intended(route('dashboard'));
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
        
        // Don't clear device_token - keep it for re-login
        // Just redirect to prefixed login page
        return redirect(prefixed_url('login'));
    }
}
