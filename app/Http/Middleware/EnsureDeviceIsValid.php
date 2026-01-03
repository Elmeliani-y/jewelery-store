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
        
        // Skip IP blocking for admin users
        if ($user && $user->isAdmin()) {
            return $next($request);
        }
        
        // Check if IP is blocked (only for non-admin users)
        if (BlockedIp::isBlocked($ipAddress)) {
            return response()->view('errors.403', [
                'message' => 'Your IP address has been blocked due to multiple failed verification attempts.'
            ], 403);
        }
        
        $token = $request->cookie('device_token');
        
        // No token means no device authentication - allow through for auth routes
        if (!$token) {
            return $next($request);
        }
        
        // Check if device exists and is valid
        $device = \App\Models\Device::where('token', $token)->first();
        
        if (!$device || !$device->active) {
            // Invalid device - record failed attempt and logout
            BlockedIp::recordFailedAttempt($ipAddress);
            
            \Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Cookie::queue(\Cookie::forget('device_token'));
            return response()->view('landing');
        }
        
        // Special handling for admin-static token - allows any authenticated user
        if ($token === 'admin-static' && $user) {
            // Valid device - reset failed attempts
            BlockedIp::resetAttempts($ipAddress);
            
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
            // Invalid device user - record failed attempt
            BlockedIp::recordFailedAttempt($ipAddress);
            
            \Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Cookie::queue(\Cookie::forget('device_token'));
            return response()->view('landing');
        }
        
        // Valid device - reset failed attempts
        BlockedIp::resetAttempts($ipAddress);
        
        return $next($request);
    }
}
