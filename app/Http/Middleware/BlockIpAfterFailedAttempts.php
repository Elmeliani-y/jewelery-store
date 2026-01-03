<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BlockIpAfterFailedAttempts
{
    /**
     * Maximum number of failed login attempts before blocking
     */
    const MAX_ATTEMPTS = 3;

    /**
     * Block duration in minutes
     */
    const BLOCK_DURATION = 60; // 1 hour

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = 'login_attempts_' . $ip;
        $blockKey = 'blocked_ip_' . $ip;

        // Check if IP is blocked
        if (Cache::has($blockKey)) {
            $blockedUntil = Cache::get($blockKey);
            Log::warning("Blocked IP attempted access: {$ip}");
            
            abort(403, 'تم حظر عنوان IP الخاص بك مؤقتاً بسبب محاولات تسجيل دخول فاشلة متعددة. يرجى المحاولة لاحقاً.');
        }

        $response = $next($request);

        // Check if this is a failed login attempt
        if ($request->is('login') && $request->isMethod('post')) {
            if ($response->status() === 302 && session()->has('errors')) {
                $attempts = Cache::get($key, 0) + 1;
                Cache::put($key, $attempts, now()->addMinutes(30));

                if ($attempts >= self::MAX_ATTEMPTS) {
                    // Block the IP
                    Cache::put($blockKey, now()->addMinutes(self::BLOCK_DURATION), now()->addMinutes(self::BLOCK_DURATION));
                    Cache::forget($key);
                    
                    Log::warning("IP blocked due to failed login attempts: {$ip}");
                    
                    return redirect('/')->with('error', 'تم حظر عنوان IP الخاص بك مؤقتاً بسبب محاولات تسجيل دخول فاشلة متعددة.');
                }
            } elseif ($response->status() === 302 && !session()->has('errors')) {
                // Successful login, clear attempts
                Cache::forget($key);
            }
        }

        return $response;
    }
}
