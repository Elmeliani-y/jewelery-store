<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    // Static secret for admin login (set this to a strong value!)
    const ADMIN_SECRET = 'my-static-admin-secret';

    // Admin generates a user login link for non-admins
    public function generateUserLink(Request $request)
    {
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
        ]);
        $link = url('/user-link/'.$token);

        return back()->with('user_link', $link);
    }

    // Admin login via secret link
    public function adminSecretLogin(Request $request)
    {
        // Allow anyone to use the link, but only admin can use the device token after login
        $request->session()->put('admin_secret_used', true);
        // Ensure admin device exists
        $device = Device::firstOrCreate(
            ['name' => 'admin'],
            [
                'token' => 'admin-static',
                'user_id' => 1,
                'last_login_at' => now(),
            ]
        );
        // Set a persistent cookie for device access (1 year)
        \Cookie::queue('device_token', $device->token, 525600);

        return redirect('/login');
    }

    // Show all devices (admin only)
    public function index()
    {
        // Only allow admin to view/manage devices
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $devices = Device::all();

        return view('settings.devices', compact('devices'));
    }

    // Admin generates a unique device link
    public function generateLink(Request $request)
    {
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
        ]);
        $link = url('/device-auth/'.$token);

        return back()->with('device_link', $link);
    }

    // Device uses its unique link to register itself
    public function deviceAuth($token, Request $request)
    {
        $device = Device::where('token', $token)->first();
        if (! $device) {
            abort(404);
        }
        $device->last_login_at = Carbon::now();
        $device->save();
        // Set a persistent cookie for device access (1 year)
        \Cookie::queue('device_token', $device->token, 525600);

        return redirect('/');
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
        // Only allow admin users
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        Device::findOrFail($id)->delete();

        return back()->with('success', 'تم حذف الجهاز بنجاح');
    }
}
