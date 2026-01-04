<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use Illuminate\Http\Request;

class BlockedIpController extends Controller
{
    /**
     * Display blocked IPs dashboard
     */
    public function index()
    {
        // Only admin can access
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $blockedIps = BlockedIp::orderBy('blocked_at', 'desc')->paginate(20);

        return view('blocked-ips.index', compact('blockedIps'));
    }

    /**
     * Unblock a specific IP
     */
    public function unblock(Request $request)
    {
        // Only admin can access
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $ipAddress = $request->input('ip_address');
        
        if (!$ipAddress) {
            return redirect()->back()->with('error', 'لم يتم تحديد عنوان IP');
        }

        BlockedIp::resetAttempts($ipAddress);

        return redirect()->back()->with('success', 'تم إلغاء حظر عنوان IP: ' . $ipAddress);
    }

    /**
     * Clear all blocked IPs
     */
    public function clearAll()
    {
        // Only admin can access
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $count = BlockedIp::whereNotNull('blocked_at')->count();
        BlockedIp::query()->delete();

        return redirect()->back()->with('success', 'تم حذف جميع عناوين IP المحظورة (' . $count . ')');
    }
}
