<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDeviceIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('device_token');
        if ($token) {
            $device = \App\Models\Device::where('token', $token)->first();
            $user = auth()->user();
            // Allow admin with admin-static token
            if ($token === 'admin-static' && $user && $user->isAdmin()) {
                return $next($request);
            }
            if (! $device || ! $device->active || ! $device->user_id || ! \App\Models\User::where('id', $device->user_id)->exists()) {
                // Invalidate session and cookie if device is deleted, inactive, or user is deleted
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
