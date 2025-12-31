<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockLoginUnlessDeviceOrAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Always allow access to login and logout
        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }
        // Block register and password/* if not device or admin
        if (
            ($request->is('register') || $request->is('password/*')) &&
            !$request->cookie('device_token') &&
            !$request->session()->get('admin_secret_used')
        ) {
            return redirect()->route('login')->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
        }
        return $next($request);
    }
}
