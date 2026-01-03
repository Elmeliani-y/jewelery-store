<?php
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\DeviceController;

/*
|--------------------------------------------------------------------------
| Web Routes - Secured Configuration
|--------------------------------------------------------------------------
| All application routes are hidden under an obfuscated prefix for security
| Root domain shows a blank landing page
|--------------------------------------------------------------------------
*/

// Define the obfuscated URL prefix
$appPrefix = env('APP_URL_PREFIX', 'b75/n95uk');

// Root domain - blank landing page
Route::get('/', function () {
    return view('landing');
});

// Admin secret login route (short link - outside prefix for convenience)
Route::get('/admin-secret/{secret}', function ($secret, Illuminate\Http\Request $request) {
    // Hash the provided secret and compare with stored hash
    $providedHash = hash('sha256', $secret);
    if (hash_equals(\App\Http\Controllers\DeviceController::ADMIN_SECRET_HASH, $providedHash)) {
        // Unblock IP when accessing valid admin secret link
        \App\Models\BlockedIp::resetAttempts($request->ip());
        return app(\App\Http\Controllers\DeviceController::class)->adminSecretLogin($request);
    }
    return view('landing'); // Don't reveal 404
})->withoutMiddleware(['auth', 'device_access']);

// Serve storage files
Route::get('storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    $logo = public_path('images/logo-login.png');
    if (file_exists($logo)) {
        return response()->file($logo);
    }
    return response('', 204);
})->where('path', '.*');

// All application routes under obfuscated prefix
Route::prefix($appPrefix)->group(function () {
    
    // Device auth route (within prefix for security)
    Route::get('/device-auth/{token}', function($token, Illuminate\Http\Request $request) {
        $device = \App\Models\Device::where('token', $token)->first();
        if (!$device) {
            abort(404);
        }
        
        // Check if link was already used by a different IP
        if ($device->first_used_at && $device->first_used_ip && $device->first_used_ip !== $request->ip()) {
            return response()->view('errors.403', ['message' => 'هذا الرابط تم استخدامه من قبل.'], 403);
        }
        
        // Mark first use
        if (!$device->first_used_at) {
            $device->first_used_at = now();
            $device->first_used_ip = $request->ip();
            $device->save();
        }
        
        // Unblock IP when accessing valid device link
        \App\Models\BlockedIp::resetAttempts($request->ip());
        return app(\App\Http\Controllers\DeviceController::class)->deviceAuth($request);
    })->withoutMiddleware(['auth', 'device_access']);

    // User link for non-admin login
    Route::get('/user-link/{token}', function ($token, Illuminate\Http\Request $request) {
        $device = \App\Models\Device::where('token', $token)->first();
        if (!$device) {
            abort(404);
        }
        
        // Check if link was already used by a different IP
        if ($device->first_used_at && $device->first_used_ip && $device->first_used_ip !== $request->ip()) {
            return response()->view('errors.403', ['message' => 'هذا الرابط تم استخدامه من قبل.'], 403);
        }
        
        // Mark first use
        if (!$device->first_used_at) {
            $device->first_used_at = now();
            $device->first_used_ip = $request->ip();
            $device->save();
        }
        
        // Unblock IP when accessing valid user link
        \App\Models\BlockedIp::resetAttempts($request->ip());
        \Cookie::queue('device_token', $device->token, 525600);
        $request->session()->put('user_link_token_used', $token);
        return redirect(prefixed_url('login'));
    })->withoutMiddleware(['auth', 'device_access']);

    // Generate user link
    Route::post('settings/devices/generate-user-link', [\App\Http\Controllers\DeviceController::class, 'generateUserLink'])
        ->name('settings.devices.generateUserLink');

    // Auth routes
    require __DIR__.'/auth.php';
    
    // Debug route - remove after testing
    Route::get('/debug-auth', function() {
        $token = request()->cookie('device_token');
        $user = auth()->user();
        $device = $token ? \App\Models\Device::where('token', $token)->first() : null;
        
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => $user ? ['id' => $user->id, 'name' => $user->name, 'role' => $user->role] : null,
            'device_token' => $token,
            'device' => $device ? ['id' => $device->id, 'name' => $device->name, 'user_id' => $device->user_id, 'active' => $device->active] : null,
            'all_devices' => \App\Models\Device::all()->map(fn($d) => ['id' => $d->id, 'token' => substr($d->token, 0, 20), 'user_id' => $d->user_id, 'active' => $d->active]),
        ]);
    })->withoutMiddleware(['auth', 'device_valid']);

    // Authenticated routes with device validation
    Route::middleware(['auth', 'device_valid'])->group(function () {
        
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])
            ->name('dashboard.chart-data');
        Route::get('dashboard/print', [DashboardController::class, 'print'])
            ->name('dashboard.print');

        // Sales
        Route::resource('sales', SaleController::class);
        Route::post('sales/{sale}/return', [SaleController::class, 'returnSale'])
            ->name('sales.return');
        Route::post('sales/{sale}/unreturn', [SaleController::class, 'unreturnSale'])
            ->name('sales.unreturn');
        Route::get('api/employees-by-branch', [SaleController::class, 'getEmployeesByBranch'])
            ->name('api.employees-by-branch');
        Route::get('api/sales/search', [SaleController::class, 'searchByInvoice'])
            ->name('api.sales.search');
        Route::get('branch/daily-sales', [SaleController::class, 'dailySales'])
            ->name('branch.daily-sales');
        Route::get('branches/sales-summary', [\App\Http\Controllers\BranchSalesController::class, 'index'])
            ->name('branches.sales-summary');
        Route::get('returns', [SaleController::class, 'returns'])
            ->name('sales.returns');

        // Expenses
        Route::resource('expenses', ExpenseController::class);
        Route::get('branch/daily-expenses', [ExpenseController::class, 'dailyExpenses'])
            ->name('branch.daily-expenses');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'all'])->name('all');
            Route::get('/index', function () {
                return redirect()->route('reports.all');
            })->name('index');
            Route::get('speed', [ReportController::class, 'speed'])->name('speed');
            Route::get('comprehensive', [ReportController::class, 'comprehensive'])->name('comprehensive');
            Route::get('detailed', [ReportController::class, 'detailed'])->name('detailed');
            Route::get('calibers', [ReportController::class, 'calibers'])->name('calibers');
            Route::get('categories', [ReportController::class, 'categories'])->name('categories');
            Route::get('employees', [ReportController::class, 'employees'])->name('employees');
            Route::get('net-profit', [ReportController::class, 'netProfit'])->name('net-profit');
            Route::get('by-branch', [ReportController::class, 'byBranch'])->name('by-branch');
            Route::get('comparative', [ReportController::class, 'comparative'])->name('comparative');
            Route::match(['get','post'], 'comparative-by-time', [ReportController::class, 'periodComparison'])
                ->name('period_comparison');
            Route::match(['get', 'post'], 'kasr', [ReportController::class, 'kasr'])->name('kasr');
            Route::get('accounts', [ReportController::class, 'accounts'])->name('accounts');
        });

        // Branches
        Route::resource('branches', BranchController::class);
        Route::post('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
            ->name('branches.toggle-status');

        // Employees
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])
            ->name('employees.toggle-status');

        // Calibers
        Route::resource('calibers', CaliberController::class)->except(['show']);
        Route::post('calibers/{caliber}/toggle-status', [CaliberController::class, 'toggleStatus'])
            ->name('calibers.toggle-status');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Expense Types
        Route::resource('expense-types', ExpenseTypeController::class)->except(['show']);

        // Users
        Route::resource('users', UserController::class);

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        // Device Management (admin only)
        Route::get('settings/devices', [\App\Http\Controllers\DeviceController::class, 'index'])
            ->name('settings.devices');
        Route::post('settings/devices/generate', [\App\Http\Controllers\DeviceController::class, 'generateLink'])
            ->name('settings.devices.generate');
        Route::delete('settings/devices/{id}', [\App\Http\Controllers\DeviceController::class, 'delete'])
            ->name('settings.devices.delete');

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
