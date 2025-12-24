<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Device;
use Carbon\Carbon;

class DeviceController extends Controller
{
    // Only allow admin
    public function index()
    {
        if (!\Illuminate\Support\Facades\Gate::allows('admin')) {
            abort(403, 'غير مصرح لك بالدخول إلى إدارة الأجهزة.');
        }
        $devices = Device::all();
        return view('settings.devices', compact('devices'));
    }

    public function generateCode(Request $request)
    {
        if (!\Illuminate\Support\Facades\Gate::allows('admin')) {
            abort(403, 'غير مصرح لك بالدخول إلى إدارة الأجهزة.');
        }
        $code = strtoupper(Str::random(6));
        Cache::put('pairing_code_' . $code, [
            'admin_id' => Auth::id(),
            'expires_at' => Carbon::now()->addMinutes(10)
        ], 600);
        return back()->with('pairing_code', $code);
    }

    public function delete($id)
    {
        if (!\Illuminate\Support\Facades\Gate::allows('admin')) {
            abort(403, 'غير مصرح لك بالدخول إلى إدارة الأجهزة.');
        }
        Device::findOrFail($id)->delete();
        return back()->with('status', 'Device deleted');
    }

    // Public pairing page
    public function showPairForm(Request $request)
    {
        $pendingUserId = $request->session()->get('pending_user_id');
        $user = $pendingUserId ? \App\Models\User::find($pendingUserId) : auth()->user();
        // If admin, skip pairing and go to dashboard
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            Auth::login($user);
            $request->session()->forget('pending_user_id');
            return redirect()->route('dashboard');
        }
        $deviceToken = $request->cookie('device_token');
        $trusted = $user && $deviceToken && \App\Models\Device::where('token', $deviceToken)->where('user_id', $user->id)->exists();
        if ($trusted) {
            Auth::login($user);
            $request->session()->forget('pending_user_id');
            return redirect()->route('dashboard');
        }
        return view('pair-device', ['pendingUser' => $user]);
    }

    public function pair(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $pendingUserId = $request->session()->get('pending_user_id');
        $user = $pendingUserId ? \App\Models\User::find($pendingUserId) : auth()->user();
        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'يجب تسجيل الدخول أولاً']);
        }
        // If admin, skip device pairing and just log in
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            Auth::login($user);
            $request->session()->forget('pending_user_id');
            return redirect('/')->with('status', 'تم تسجيل الدخول بنجاح');
        }
        $data = Cache::get('pairing_code_' . strtoupper($request->code));
        if (!$data || Carbon::now()->gt($data['expires_at'])) {
            return back()->withErrors(['code' => 'Invalid or expired code']);
        }
        // Use username and browser info as device name
        $deviceName = ($user ? $user->username : 'User') . ' - ' . ($request->userAgent() ?: 'Unknown');
        $device = Device::create([
            'name' => $deviceName,
            'user_id' => $user->id,
            'token' => hash('sha256', Str::random(32)),
            'last_login_at' => now(),
        ]);
        // Set device cookie for 1 year
        \Cookie::queue('device_token', $device->token, 525600);
        Cache::forget('pairing_code_' . strtoupper($request->code));
        // Log in user and clear pending
        Auth::login($user);
        $request->session()->forget('pending_user_id');
        return redirect('/')->with('status', 'Device paired successfully');
    }
}
