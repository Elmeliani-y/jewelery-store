<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

class PasswordCodeController extends Controller
{
    // Step 1: Request code
    public function requestCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $code = random_int(100000, 999999);
        $expires = now()->addMinutes(10);
        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            ['code' => $code, 'expires_at' => $expires, 'created_at' => now(), 'updated_at' => now()]
        );
        // Send code by email in Arabic and styled
        $html = '<div style="direction:rtl;text-align:right;font-family:tahoma,arial,sans-serif;background:#f9f9f9;padding:24px;border-radius:8px;max-width:400px;margin:auto;">
            <h2 style="color:#2d3748;">استعادة كلمة المرور</h2>
            <p>رمز الاستعادة الخاص بك هو:</p>
            <div style="font-size:2em;font-weight:bold;color:#007bff;margin:16px 0;">' . $code . '</div>
            <p>يرجى إدخال هذا الرمز في صفحة استعادة كلمة المرور في التطبيق. هذا الرمز صالح لمدة 10 دقائق فقط.</p>
            <hr style="margin:24px 0;">
            <p style="color:#888;font-size:0.9em;">إذا لم تطلب استعادة كلمة المرور، يمكنك تجاهل هذه الرسالة.</p>
        </div>';
        Mail::send([], [], function ($message) use ($request, $html) {
            $message->to($request->email)
                ->subject('رمز استعادة كلمة المرور')
                ->html($html);
        });
        return back()->with([
            'status' => 'تم إرسال رمز الاستعادة إلى بريدك الإلكتروني.',
            'show_code_form' => true,
            'email' => $request->email
        ]);
    }

    // Step 2: Show code entry form
    public function showCodeForm()
    {
        return view('auth.passwords.email');
    }

    // Step 3: Verify code and show reset form
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);
        $row = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();
        if (!$row) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }
        // Show password reset form
        return view('auth.passwords.reset', ['email' => $request->email, 'code' => $request->code]);
    }

    // Step 4: Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|confirmed|min:8',
        ]);
        $row = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();
        if (!$row) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        // Optionally delete the code
        DB::table('password_reset_codes')->where('email', $request->email)->delete();
        return redirect()->route('login')->with('status', 'Password reset successful!');
    }
}
