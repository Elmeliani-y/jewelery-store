<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DeviceController;

class EnforceDeviceAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Allow admins to bypass device check
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }
        // Allow device registration/auth and admin secret login routes
        $allowed = [
            'device.auth',
            'admin.secret.login',
            'login',
            'logout',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
        ];
        if ($request->route() && in_array($request->route()->getName(), $allowed)) {
            return $next($request);
        }
        // Enforce device access
        DeviceController::checkDeviceAccess($request);
        return $next($request);
    }
}
