<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedIp;
use Illuminate\Support\Facades\Cache;

class ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();
        
        // Skip throttling for admin secret links (allow them to unblock)
        if ($request->is('admin-secret/*') || 
            $request->is('*/device-auth/*') || 
            $request->is('*/user-link/*')) {
            return $next($request);
        }
        
        // Skip throttling for authenticated users with valid device tokens
        if (auth()->check()) {
            $user = auth()->user();
            // Check if user has a valid device token in session
            if (session()->has('device_token') || $user->is_admin) {
                return $next($request);
            }
        }
        
        // Check if IP is blocked
        if (BlockedIp::isBlocked($ipAddress)) {
            return response()->view('errors.403', [
                'message' => 'Your IP address has been blocked due to excessive requests.'
            ], 403);
        }
        
        // Use cache to track requests per IP
        $cacheKey = "request_count_{$ipAddress}";
        $requests = Cache::get($cacheKey, 0);
        
        // Increment request count
        $requests++;
        Cache::put($cacheKey, $requests, now()->addMinute());
        
        // Block after 3 requests within 1 minute
        if ($requests > 3) {
            BlockedIp::recordFailedAttempt($ipAddress);
            BlockedIp::recordFailedAttempt($ipAddress);
            BlockedIp::recordFailedAttempt($ipAddress); // Force block immediately
            
            return response()->view('errors.403', [
                'message' => 'Your IP address has been blocked due to excessive requests.'
            ], 403);
        }
        
        return $next($request);
    }
}
