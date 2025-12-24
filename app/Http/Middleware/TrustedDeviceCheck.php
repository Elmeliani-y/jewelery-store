<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;

class TrustedDeviceCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Only check for authenticated users
        if (Auth::check()) {
            // Exclude admin users from device trust check (admin is always trusted)
            if (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()) {
                return $next($request);
            }
            // Allow access to pairing routes without device check
            $pairingRoutes = [
                'pair-device.form',
                'pair-device.pair',
                'logout', // optionally allow logout
            ];
            if ($request->route() && in_array($request->route()->getName(), $pairingRoutes, true)) {
                return $next($request);
            }
            $deviceToken = $request->cookie('device_token');
            if (!$deviceToken || !Device::where('token', $deviceToken)->where('user_id', Auth::id())->exists()) {
                // If not trusted, redirect to pairing page
                return redirect()->route('pair-device.form');
            }
        }
        return $next($request);
    }
}
