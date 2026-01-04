<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BlockedIp;

class EnsureDeviceIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $request->ip();
        $user = auth()->user();
        
        $token = $request->cookie('device_token');
        
        // No token means no device authentication - allow through for auth routes
        if (!$token) {
            return $next($request);
        }
        
        // Check if device exists and is valid
        $device = \App\Models\Device::where('token', $token)->first();
        
        if (!$device || !$device->active) {
            // Invalid device - logout
            \Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Cookie::queue(\Cookie::forget('device_token'));
            return response()->view('landing');
        }
        
        // Special handling for admin-static token - allows any authenticated user
        if ($token === 'admin-static' && $user) {
            // Update device last_login_at
            $device->last_login_at = now();
            if (!$device->user_id || ($user->isAdmin() && $device->user_id !== $user->id)) {
                $device->user_id = $user->id;
            }
            $device->save();
            return $next($request);
        }
        
        // For non-admin-static devices, check if device has valid user_id
        if (!$device->user_id || !\App\Models\User::where('id', $device->user_id)->exists()) {
            // Invalid device user
            \Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Cookie::queue(\Cookie::forget('device_token'));
            return response()->view('landing');
        }
        
        return $next($request);
    }
}
