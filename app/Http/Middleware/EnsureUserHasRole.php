<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:admin,accountant')
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        // Support either comma-separated single param or variadic params
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }
        $allowed = array_map('trim', $roles);

        if (!$user || !in_array($user->role, $allowed, true)) {
            abort(403, 'غير مسموح لك بالوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
