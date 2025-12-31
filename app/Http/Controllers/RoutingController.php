<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoutingController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // $this->
        // middleware('auth')->
        // except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()) {
            if (Auth::user()->isAccountant()) {
                // Accountants go to /
                return view('welcome'); // or any default accountant view, or just return redirect('/') if you want loop
            }
            return redirect()->route('dashboard');
        }
        $hasDevice = $request->cookie('device_token');
        $hasAdminSecret = $request->session()->get('admin_secret_used');
        $hasUserLink = $request->session()->get('user_link_token_used');
        if ($hasDevice || $hasAdminSecret || $hasUserLink) {
            // Instead of redirecting, show the login view directly
            return view('auth.login');
        }
        // Otherwise, abort with 404 to prevent redirect loop
        abort(404);
    }

    /**
     * Display a view based on first route param
     *
     * @return \Illuminate\Http\Response
     */
    public function root(Request $request, $first)
    {
        return view($first);
    }

    /**
     * second level route
     */
    public function secondLevel(Request $request, $first, $second)
    {
        return view($first . '.' . $second);
    }

    /**
     * third level route
     */
    public function thirdLevel(Request $request, $first, $second, $third)
    {
        // Prevent trying to render storage files as views
        if ($first === 'storage') {
            abort(404);
        }
        return view($first . '.' . $second . '.' . $third);
    }
}
