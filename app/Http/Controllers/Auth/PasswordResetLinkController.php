<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        if (!$request->cookie('device_token') && !$request->session()->get('admin_secret_used')) {
            return redirect(url(env('APP_URL_PREFIX', 'xK9wR2vP8nL4tY6zA5bM3cH0jG7eF1dQ') . '/k2m7n3p8'))->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
        }
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if (!$request->cookie('device_token') && !$request->session()->get('admin_secret_used')) {
            return redirect(url(env('APP_URL_PREFIX', 'xK9wR2vP8nL4tY6zA5bM3cH0jG7eF1dQ') . '/k2m7n3p8'))->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
        }
        $request->validate([
            'email' => 'required|email',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
