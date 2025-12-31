<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDeviceIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('device_token');
        $user = auth()->user();
        if ($token) {
            // Allow admin with admin-static token regardless of device record
            if ($token === 'admin-static' && $user && $user->isAdmin()) {
                return $next($request);
            }
            $device = \App\Models\Device::where('token', $token)->first();
            if (! $device || ! $device->active || ! $device->user_id || ! \App\Models\User::where('id', $device->user_id)->exists()) {
                \Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                \Cookie::queue(\Cookie::forget('device_token'));
                abort(404);
            }
        }
        return $next($request);
    }
}
