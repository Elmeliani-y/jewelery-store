<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BlockedIp;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();
        
        // Only bypass if CURRENTLY accessing admin secret URL (not just having session)
        $currentPath = $request->path();
        $adminSecret = env('ADMIN_SECRET', 'pK3e8fnQjgrykS7RamqNuGcC4D2sBVLF9Zbt65WA');
        
        // Check if current URL contains admin secret
        if (strpos($currentPath, $adminSecret) !== false) {
            return $next($request);
        }
        
        // Check if IP is blocked - NO OTHER EXCEPTIONS
        if (BlockedIp::isBlocked($ipAddress)) {
            // Clear all sessions and cookies when showing ban page
            $request->session()->flush();
            \Cookie::queue(\Cookie::forget('device_token'));
            
            return response()->view('errors.403', [
                'message' => 'تم حظر عنوان IP الخاص بك مؤقتاً بسبب محاولات تسجيل دخول فاشلة متعددة. يرجى التواصل مع المسؤول لإلغاء الحظر.'
            ], 403);
        }

        return $next($request);
    }
}
