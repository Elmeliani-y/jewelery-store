<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        if (!$request->cookie('device_token') && !$request->session()->get('admin_secret_used')) {
            return redirect(url(env('APP_URL_PREFIX', 'xK9wR2vP8nL4tY6zA5bM3cH0jG7eF1dQ') . '/k2m7n3p8'))->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
        }
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        if (!$request->cookie('device_token') && !$request->session()->get('admin_secret_used')) {
            return redirect(url(env('APP_URL_PREFIX', 'xK9wR2vP8nL4tY6zA5bM3cH0jG7eF1dQ') . '/k2m7n3p8'))->with('admin_only_error', 'هذه الصفحة مخصصة فقط للمدير.');
        }
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
