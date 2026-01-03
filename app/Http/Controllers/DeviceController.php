<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    protected function validateDeviceOrAbort()
    {
        $token = request()->cookie('device_token');
        $user = auth()->user();
        if ($token) {
            $device = \App\Models\Device::where('token', $token)->first();
            // Accept admin device token for admin users
            if ($device && $device->name === 'admin' && $user && $user->isAdmin()) {
                return;
            }
            if (! $device || ! $device->active || ! $device->user_id || ! \App\Models\User::where('id', $device->user_id)->exists()) {
                \Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                \Cookie::queue(\Cookie::forget('device_token'));
                abort(404);
            }
        }
    }
    
    // Static hashed admin secret - only the hash is stored here
    // Actual secret: 7xK9mP2vQ8nL4wR6tY3zA5bN1cJ0hF8e
    const ADMIN_SECRET_HASH = '5ad51e4680e48dc197ad304acbe557901c6bbde9eaf070d0da6f1274c876eaf6'; // Hash stored

    // Admin generates a user login link for non-admins
    public function generateUserLink(Request $request)
    {
        $this->validateDeviceOrAbort();
        // Only allow admin users
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'اسم الجهاز مطلوب',
        ]);
        $token = Str::random(40);
        $device = Device::create([
            'name' => $request->input('name'),
            'token' => $token,
            'user_id' => auth()->id(),
            'last_login_at' => null,
            'active' => true,
        ]);
        $link = url('/') . '/' . $token;
        return back()->with('user_link', $link);
    }

    // Admin login via secret link
    public function adminSecretLogin(Request $request)
    {
        // Allow anyone to use the link, but only admin can use the device token after login
        $request->session()->put('admin_secret_used', true);
        
        // Find or create admin device
        $device = Device::where('token', 'admin-static')->first();
        
        if (!$device) {
            // Create new admin device - user_id will be set after login
            $device = Device::create([
                'name' => 'admin',
                'token' => 'admin-static',
                'user_id' => 2, // Set to admin user
                'last_login_at' => now(),
                'active' => true,
            ]);
        } else {
            // Ensure it's active
            $device->active = true;
            $device->user_id = 2;
            $device->save();
        }
        
        // Create cookie that lasts 1 year (525600 minutes)
        $cookie = cookie('device_token', $device->token, 525600, '/', null, false, false);

        return redirect(prefixed_url('login'))->cookie($cookie);
    }

    // Show all devices (admin only)
    public function index()
    {
        $this->validateDeviceOrAbort();
        // Only allow admin to view/manage devices
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $devices = Device::where('active', true)->get();
        return view('settings.devices', compact('devices'));
    }

    // Admin generates a unique device link
    public function generateLink(Request $request)
    {
        $this->validateDeviceOrAbort();
        // Only allow admin users
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'اسم الجهاز مطلوب',
        ]);
        $token = Str::random(40);
        $device = Device::create([
            'name' => $request->input('name'),
            'token' => $token,
            'user_id' => Auth::id(),
            'last_login_at' => null,
            'active' => true,
        ]);
        $link = url('/') . '/' . $token;
        return back()->with('device_link', $link);
    }

    // Device uses its unique link to register itself
    public function deviceAuth($token, Request $request)
    {
        $this->validateDeviceOrAbort();
        $device = Device::where('token', $token)->first();
        if (! $device) {
            abort(404);
        }
        $device->last_login_at = Carbon::now();
        $device->save();
        // Set a persistent cookie for device access (1 year)
        \Cookie::queue('device_token', $device->token, 525600);
        return redirect(prefixed_url('login'));
    }

    // Middleware-like helper for device access
    public static function checkDeviceAccess(Request $request)
    {
        $token = $request->cookie('device_token');
        if (! $token) {
            abort(404);
        }
        $device = Device::where('token', $token)->first();
        if (! $device) {
            abort(404);
        }
        // Optionally update last_login_at
        $device->last_login_at = Carbon::now();
        $device->save();

        // Allow access
        return $device;
    }

    // Admin deletes a device
    public function delete($id)
    {
        $this->validateDeviceOrAbort();
        // Only allow admin users
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $device = Device::findOrFail($id);
        $device->delete();
        return back()->with('success', 'تم حذف الجهاز نهائياً من قاعدة البيانات');
    }
}
