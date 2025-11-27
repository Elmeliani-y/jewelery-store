<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictBranchFromDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && method_exists($user, 'isBranch') && $user->isBranch()) {
            return redirect()->route('sales.create')
                ->with('warning', 'تم تحويلك إلى صفحة تسجيل المبيعة. حساب الفرع لا يمكنه فتح لوحة التحكم.');
        }

        return $next($request);
    }
}
