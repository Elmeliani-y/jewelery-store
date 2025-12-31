<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;

class SecretLinkDeviceRegister
{
    public function handle(Request $request, Closure $next)
    {
        $cookieName = 'device_token';
        $deviceToken = $request->cookie($cookieName);
        $user = Auth::user();

        // إذا الجهاز لديه توكن صحيح في الكوكي وقاعدة البيانات
        if ($deviceToken && Device::where('token', $deviceToken)->where('active', true)->exists()) {
            return $next($request);
        }

        // إذا دخل برابط سري
        $secret = $request->query('secret');
        if ($secret && $user) {
            // أنشئ توكن جديد للجهاز
            $newToken = hash('sha256', Str::random(32));
            Device::create([
                'name' => ($user->username ?? $user->email ?? 'User') . ' - ' . ($request->userAgent() ?: 'Unknown'),
                'user_id' => $user->id,
                'token' => $newToken,
                'user_agent' => $request->userAgent(),
                'last_login_at' => now(),
                'active' => true,
            ]);
            // احفظ التوكن في الكوكي
            Cookie::queue($cookieName, $newToken, 525600);
            return $next($request);
        }

        // غير مسموح له → رسالة خطأ منبثقة
        return redirect()->back()->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
    }
}
