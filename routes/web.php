// Debug route to check server time
Route::get('/debug-server-time', function () {
    return response()->json([
        'laravel_now' => now()->toDateTimeString(),
        'php_date' => date('c'),
        'timezone' => config('app.timezone'),
    ]);
});
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

// Admin generates a user login link for non-admins
Route::post('settings/devices/generate-user-link', [DeviceController::class, 'generateUserLink'])->name('settings.devices.generateUserLink');
// User link for non-admin login (no device cookie required)
Route::get('/user-link/{token}', function ($token, Illuminate\Http\Request $request) {
    $device = \App\Models\Device::where('token', $token)->first();
    if (!$device) {
        abort(404);
    }
    // Set device_token cookie for 1 year
    \Cookie::queue('device_token', $device->token, 525600);
    $request->session()->put('user_link_token_used', $token);
    return redirect('/login');
})->withoutMiddleware(['auth', 'device_access']);

// Root route: redirect to dashboard or login
Route::get('/', [RoutingController::class, 'index']);

// --- Secret/Admin/Device Auth Logic ---
// Admin secret login route (static secret in controller)
Route::get('/admin-secret/{secret}', function ($secret, Illuminate\Http\Request $request) {
    if ($secret === DeviceController::ADMIN_SECRET) {
        return app(DeviceController::class)->adminSecretLogin($request);
    }
    abort(404);
})->withoutMiddleware(['auth', 'device_access']);

// Device auth route (for branches/devices)
Route::get('/device-auth/{token}', [DeviceController::class, 'deviceAuth'])->withoutMiddleware(['auth', 'device_access']);

// Auth and login routes (404 logic will be handled in controllers)
require __DIR__.'/auth.php';

// Serve storage files directly to avoid RoutingController catching them
Route::get('storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    // Use the login logo as the placeholder image instead of 404
    $logo = public_path('images/logo-login.png');
    if (file_exists($logo)) {
        return response()->file($logo);
    }
    // If no logo, return a 204 No Content so nothing is shown
    return response('', 204);
})->where('path', '.*')->withoutMiddleware(['auth']);

// Authenticated routes: all require device access except device registration/auth and admin secret login
Route::group(['middleware' => ['auth', 'device_valid']], function () {
    Route::get('', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('dashboard/print', [DashboardController::class, 'print'])->name('dashboard.print');
    Route::resource('sales', SaleController::class);
    Route::post('sales/{sale}/return', [SaleController::class, 'returnSale'])->name('sales.return');
    Route::post('sales/{sale}/unreturn', [SaleController::class, 'unreturnSale'])->name('sales.unreturn');
    Route::get('api/employees-by-branch', [SaleController::class, 'getEmployeesByBranch'])->name('api.employees-by-branch');
    Route::get('api/sales/search', [SaleController::class, 'searchByInvoice'])->name('api.sales.search');
    Route::get('branch/daily-sales', [SaleController::class, 'dailySales'])->name('branch.daily-sales');
    Route::get('branches/sales-summary', [\App\Http\Controllers\BranchSalesController::class, 'index'])->name('branches.sales-summary');
    Route::get('returns', [SaleController::class, 'returns'])->name('sales.returns');
    Route::resource('expenses', ExpenseController::class);
    Route::get('branch/daily-expenses', [ExpenseController::class, 'dailyExpenses'])->name('branch.daily-expenses');
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
        Route::match(['get','post'], 'comparative-by-time', [ReportController::class, 'periodComparison'])->name('period_comparison');
        Route::match(['get', 'post'], 'kasr', [ReportController::class, 'kasr'])->name('kasr');
        Route::get('accounts', [ReportController::class, 'accounts'])->name('accounts');
    });
    Route::resource('branches', BranchController::class);
    Route::post('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::resource('calibers', CaliberController::class)->except(['show']);
    Route::post('calibers/{caliber}/toggle-status', [CaliberController::class, 'toggleStatus'])->name('calibers.toggle-status');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('expense-types', ExpenseTypeController::class)->except(['show']);
    Route::resource('users', UserController::class);
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // Devices management (admin only)
    Route::get('settings/devices', [DeviceController::class, 'index'])->name('settings.devices');
    Route::post('settings/devices/generate', [DeviceController::class, 'generateLink'])->name('settings.devices.generate');
    Route::delete('settings/devices/{id}', [DeviceController::class, 'delete'])->name('settings.devices.delete');

    // ...existing code for other resources and settings...

    // Legacy routes for existing template compatibility (must be last)
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
// Admin generates a user login link for non-admins
Route::post('settings/devices/generate-user-link', [\App\Http\Controllers\DeviceController::class, 'generateUserLink'])->name('settings.devices.generateUserLink');
// User link for non-admin login (no device cookie required)
Route::get('/user-link/{token}', function ($token, \Illuminate\Http\Request $request) {
    // You can add token validation here if you want to restrict to valid tokens only
    $request->session()->put('user_link_token_used', $token);
    return redirect('/login');
})->withoutMiddleware(['auth', 'device_access']);

// Root route: redirect to dashboard or login
Route::get('/', [RoutingController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




// --- Secret/Admin/Device Auth Logic ---
use App\Http\Controllers\DeviceController;

// Admin secret login route (static secret in controller)
Route::get('/admin-secret/{secret}', function ($secret, \Illuminate\Http\Request $request) {
    if ($secret === DeviceController::ADMIN_SECRET) {
        return app(DeviceController::class)->adminSecretLogin($request);
    }
    abort(404);
})->withoutMiddleware(['auth', 'device_access']);

// Device auth route (for branches/devices)
Route::get('/device-auth/{token}', [DeviceController::class, 'deviceAuth'])->withoutMiddleware(['auth', 'device_access']);

// Auth and login routes (404 logic will be handled in controllers)
require __DIR__.'/auth.php';


Route::get('storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    // Use the login logo as the placeholder image instead of 404
    $logo = public_path('images/logo-login.png');
    if (file_exists($logo)) {
        return response()->file($logo);
    }
    // If no logo, return a 204 No Content so nothing is shown
    return response('', 204);
})->where('path', '.*')->withoutMiddleware(['auth']);

// Authenticated routes: all require device access except device registration/auth and admin secret login
Route::group(['middleware' => ['auth']], function () {
    Route::get('', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('dashboard/print', [DashboardController::class, 'print'])->name('dashboard.print');
    Route::resource('sales', SaleController::class);
    Route::post('sales/{sale}/return', [SaleController::class, 'returnSale'])->name('sales.return');
    Route::post('sales/{sale}/unreturn', [SaleController::class, 'unreturnSale'])->name('sales.unreturn');
    Route::get('api/employees-by-branch', [SaleController::class, 'getEmployeesByBranch'])->name('api.employees-by-branch');
    Route::get('api/sales/search', [SaleController::class, 'searchByInvoice'])->name('api.sales.search');
    Route::get('branch/daily-sales', [SaleController::class, 'dailySales'])->name('branch.daily-sales');
    Route::get('branches/sales-summary', [\App\Http\Controllers\BranchSalesController::class, 'index'])->name('branches.sales-summary');
    Route::get('returns', [SaleController::class, 'returns'])->name('sales.returns');
    Route::resource('expenses', ExpenseController::class);
    Route::get('branch/daily-expenses', [ExpenseController::class, 'dailyExpenses'])->name('branch.daily-expenses');
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
        Route::match(['get','post'], 'comparative-by-time', [ReportController::class, 'periodComparison'])->name('period_comparison');
        Route::match(['get', 'post'], 'kasr', [ReportController::class, 'kasr'])->name('kasr');
        Route::get('accounts', [ReportController::class, 'accounts'])->name('accounts');
    });
    Route::resource('branches', BranchController::class);
    Route::post('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::resource('calibers', CaliberController::class)->except(['show']);
    Route::post('calibers/{caliber}/toggle-status', [CaliberController::class, 'toggleStatus'])->name('calibers.toggle-status');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('expense-types', ExpenseTypeController::class)->except(['show']);
    Route::resource('users', UserController::class);
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // Devices management (admin only)
    Route::get('settings/devices', [\App\Http\Controllers\DeviceController::class, 'index'])->name('settings.devices');
    Route::post('settings/devices/generate', [\App\Http\Controllers\DeviceController::class, 'generateLink'])->name('settings.devices.generate');
    Route::delete('settings/devices/{id}', [\App\Http\Controllers\DeviceController::class, 'delete'])->name('settings.devices.delete');

    // ...existing code for other resources and settings...

    // Legacy routes for existing template compatibility (must be last)
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
