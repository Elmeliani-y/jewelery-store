<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CaliberController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Secured Configuration
|--------------------------------------------------------------------------
| All application routes are hidden under an obfuscated prefix for security
| Root domain shows a blank landing page
|--------------------------------------------------------------------------
*/

// Define the obfuscated URL prefix (random encrypted-looking string)
$appPrefix = env('APP_URL_PREFIX', 'HgAdTY3thJXCvZUWs5M4Q2yEnwaLk8ufrRxNe6zp');
$adminSecret = env('ADMIN_SECRET', 'pK3e8fnQjgrykS7RamqNuGcC4D2sBVLF9Zbt65WA');

// Root domain - blank landing page
Route::get('/', function () {
    return view('landing');
});

// Serve storage files
Route::get('storage/{path}', function ($path) {
    $file = storage_path('app/public/'.$path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    $logo = public_path('images/logo-login.png');
    if (file_exists($logo)) {
        return response()->file($logo);
    }

    return response('', 204);
})->where('path', '.*');

// Device auth route at root level (short URL)
Route::get('/{token}', function ($token, Illuminate\Http\Request $request) use ($appPrefix, $adminSecret) {
    // Admin secret: 32-40 chars (flexible for new secret)
    if (strlen($token) >= 32 && strlen($token) <= 40) {
        $providedHash = hash('sha256', $token);
        if (hash_equals(\App\Http\Controllers\DeviceController::ADMIN_SECRET_HASH, $providedHash)) {
            // Unblock IP when accessing valid admin secret link
            \App\Models\BlockedIp::resetAttempts($request->ip());

            return app(\App\Http\Controllers\DeviceController::class)->adminSecretLogin($request);
        }
    }

    // Device/User token: 40 chars
    if (strlen($token) !== 40) {
        abort(404);
    }
    $device = \App\Models\Device::where('token', $token)->first();
    if (! $device) {
        abort(404);
    }

    // Mark first use (removed IP restriction to allow dynamic IPs)
    if (! $device->first_used_at) {
        $device->first_used_at = now();
        $device->first_used_ip = $request->ip();
        $device->save();
    }

    // DON'T unblock IP for device links - let admin unblock manually
    // Only admin links can unblock IPs

    // Clear any existing device token and session data
    \Cookie::queue(\Cookie::forget('device_token'));
    $request->session()->forget('user_link_token_used');
    $request->session()->forget('admin_secret_used');
    
    // Set new device token and session
    \Cookie::queue('device_token', $device->token, 525600);
    $request->session()->put('user_link_token_used', $token);

    return redirect('/' . $appPrefix . '/k2m7n3p8');
})->withoutMiddleware(['auth', 'device_access']);

// All application routes under obfuscated prefix
Route::prefix($appPrefix)->group(function () {

    // Auth routes
    require __DIR__.'/auth.php';

    // Generate user link
    Route::post('w9x4y7z2/a5b1c8d3/e6f2g9h4', [\App\Http\Controllers\DeviceController::class, 'generateUserLink'])
        ->name('q3r8s1t6.u4v9w2x7.y5z1a8b3');

    // Debug route - remove after testing
    Route::get('/debug-auth', function () {
        $token = request()->cookie('device_token');
        $user = auth()->user();
        $device = $token ? \App\Models\Device::where('token', $token)->first() : null;

        return response()->json([
            'authenticated' => auth()->check(),
            'user' => $user ? ['id' => $user->id, 'name' => $user->name, 'role' => $user->role] : null,
            'device_token' => $token,
            'device' => $device ? ['id' => $device->id, 'name' => $device->name, 'user_id' => $device->user_id, 'active' => $device->active] : null,
            'all_devices' => \App\Models\Device::all()->map(fn ($d) => ['id' => $d->id, 'token' => substr($d->token, 0, 20), 'user_id' => $d->user_id, 'active' => $d->active]),
        ]);
    })->withoutMiddleware(['auth', 'device_valid']);

    // Authenticated routes with device validation
    Route::middleware(['auth', 'device_valid'])->group(function () {

        // Dashboard
        Route::get('c5d9f2h7', [DashboardController::class, 'index'])->name('c5d9f2h7');
        Route::get('c5d9f2h7/j4k8m1n6', [DashboardController::class, 'getChartData'])
            ->name('c5d9f2h7.j4k8m1n6');
        Route::get('c5d9f2h7/p3q7r9s2', [DashboardController::class, 'print'])
            ->name('c5d9f2h7.p3q7r9s2');

        // Sales
        Route::resource('t6u1v5w8', SaleController::class);
        Route::post('t6u1v5w8/{sale}/x2y7z3a9', [SaleController::class, 'returnSale'])
            ->name('t6u1v5w8.x2y7z3a9');
        Route::post('t6u1v5w8/{sale}/b4c8d1e5', [SaleController::class, 'unreturnSale'])
            ->name('t6u1v5w8.b4c8d1e5');
        Route::get('f9g2h6i3/j7k1l4m8', [SaleController::class, 'getEmployeesByBranch'])
            ->name('f9g2h6i3.j7k1l4m8');
        Route::get('f9g2h6i3/t6u1v5w8/n5o9p2q6', [SaleController::class, 'searchByInvoice'])
            ->name('f9g2h6i3.t6u1v5w8.n5o9p2q6');
        Route::get('r8s3t7u1/v4w9x2y5', [SaleController::class, 'dailySales'])
            ->name('r8s3t7u1.v4w9x2y5');
        Route::get('z6a1b5c9/d3e7f2g8', [\App\Http\Controllers\BranchSalesController::class, 'index'])
            ->name('z6a1b5c9.d3e7f2g8');
        Route::get('h1i5j9k3', [SaleController::class, 'returns'])
            ->name('h1i5j9k3');

        // Expenses
        Route::resource('l7m2n6o1', ExpenseController::class);
        Route::get('r8s3t7u1/p4q9r5s2', [ExpenseController::class, 'dailyExpenses'])
            ->name('r8s3t7u1.p4q9r5s2');

        // Reports
        Route::prefix('t3u8v1w4')->name('t3u8v1w4.')->group(function () {
            Route::get('/', [ReportController::class, 'all'])->name('b1c5d8e3');
            Route::get('/x7y2z6a9', function () {
                return redirect()->route('t3u8v1w4.b1c5d8e3');
            })->name('x7y2z6a9');
            Route::get('b1c5d8e3', [ReportController::class, 'speed'])->name('f4g9h2i7');
            Route::get('f4g9h2i7', [ReportController::class, 'comprehensive'])->name('j6k1l5m9');
            Route::get('j6k1l5m9', [ReportController::class, 'detailed'])->name('n3o7p2q8');
            Route::get('n3o7p2q8', [ReportController::class, 'calibers'])->name('r5s9t4u1');
            Route::get('r5s9t4u1', [ReportController::class, 'categories'])->name('v8w3x7y2');
            Route::get('v8w3x7y2', [ReportController::class, 'employees'])->name('z1a6b9c4');
            Route::get('z1a6b9c4', [ReportController::class, 'netProfit'])->name('d5e2f8g3');
            Route::get('d5e2f8g3', [ReportController::class, 'byBranch'])->name('h7i1j5k9');
            Route::get('h7i1j5k9', [ReportController::class, 'comparative'])->name('l3m8n2o6');
            Route::match(['get', 'post'], 'l3m8n2o6', [ReportController::class, 'periodComparison'])
                ->name('p4q9r1s7');
            Route::match(['get', 'post'], 'p4q9r1s7', [ReportController::class, 'kasr'])->name('t6u2v8w5');
            Route::get('t6u2v8w5', [ReportController::class, 'accounts'])->name('a3b7c1d5');
        });

        // Branches
        Route::resource('x9y4z1a6', BranchController::class);
        Route::post('x9y4z1a6/{branch}/b2c7d5e8', [BranchController::class, 'toggleStatus'])
            ->name('x9y4z1a6.b2c7d5e8');

        // Employees
        Route::resource('f3g8h1i4', EmployeeController::class);
        Route::post('f3g8h1i4/{employee}/j9k5l2m7', [EmployeeController::class, 'toggleStatus'])
            ->name('f3g8h1i4.j9k5l2m7');

        // Calibers
        Route::resource('n6o1p4q9', CaliberController::class)->except(['show']);
        Route::post('n6o1p4q9/{caliber}/r2s8t3u7', [CaliberController::class, 'toggleStatus'])
            ->name('n6o1p4q9.r2s8t3u7');

        // Categories
        Route::resource('v5w9x4y1', CategoryController::class)->except(['show']);

        // Expense Types
        Route::resource('z8a3b6c2', ExpenseTypeController::class)->except(['show']);

        // Users
        Route::resource('d7e1f5g9', UserController::class);

        // Settings
        Route::get('h4i8j3k7', [SettingController::class, 'index'])->name('h4i8j3k7');
        Route::post('h4i8j3k7', [SettingController::class, 'update'])->name('h4i8j3k7.update');

        // Device Management (admin only)
        Route::get('h4i8j3k7/l2m6n9o4', [\App\Http\Controllers\DeviceController::class, 'index'])
            ->name('h4i8j3k7.l2m6n9o4');
        Route::post('h4i8j3k7/l2m6n9o4/p1q5r8s3', [\App\Http\Controllers\DeviceController::class, 'generateLink'])
            ->name('h4i8j3k7.l2m6n9o4.p1q5r8s3');
        Route::delete('h4i8j3k7/l2m6n9o4/{id}', [\App\Http\Controllers\DeviceController::class, 'delete'])
            ->name('h4i8j3k7.l2m6n9o4.delete');

        // Blocked IPs Management (admin only)
        Route::get('q8r2s6t0', [\App\Http\Controllers\BlockedIpController::class, 'index'])
            ->name('q8r2s6t0');
        Route::post('q8r2s6t0/unblock', [\App\Http\Controllers\BlockedIpController::class, 'unblock'])
            ->name('q8r2s6t0.unblock');
        Route::post('q8r2s6t0/clear', [\App\Http\Controllers\BlockedIpController::class, 'clearAll'])
            ->name('q8r2s6t0.clear');

        // Legacy routes for template compatibility (must be last)
        Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])
            ->name('third');
        Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])
            ->name('second');
        Route::get('{any}', [RoutingController::class, 'root'])
            ->name('any');
    });
});

// Catch all other routes - show blank page (don't reveal 404)
Route::fallback(function () {
    return view('landing');
});
