// Serve storage files directly to avoid RoutingController catching them
Route::get('storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    abort(404);
})->where('path', '.*');
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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__.'/auth.php';

Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {

    // Original Dashboard route at root
    Route::get('', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('dashboard/print', [DashboardController::class, 'print'])->name('dashboard.print');

    // Sales Management (original single resource)
    Route::resource('sales', SaleController::class);
    Route::post('sales/{sale}/return', [SaleController::class, 'returnSale'])->name('sales.return');
    Route::post('sales/{sale}/unreturn', [SaleController::class, 'unreturnSale'])->name('sales.unreturn');
    Route::get('api/employees-by-branch', [SaleController::class, 'getEmployeesByBranch'])->name('api.employees-by-branch');
    Route::get('api/sales/search', [SaleController::class, 'searchByInvoice'])->name('api.sales.search');
    Route::get('branch/daily-sales', [SaleController::class, 'dailySales'])->name('branch.daily-sales');

    // Returns dashboard
    Route::get('returns', [SaleController::class, 'returns'])->name('sales.returns');

    // Expenses Management (original single resource)
    Route::resource('expenses', ExpenseController::class);
    Route::get('branch/daily-expenses', [ExpenseController::class, 'dailyExpenses'])->name('branch.daily-expenses');

    // Reports (unrestricted as original)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'all'])->name('all');
        Route::get('/index', function() {
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
        Route::match(['get', 'post'], 'kasr', [ReportController::class, 'kasr'])->name('kasr');
        Route::get('accounts', [ReportController::class, 'accounts'])->name('accounts');
    });

    // Branches Management
    Route::resource('branches', BranchController::class);
    Route::post('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');

    // Employees Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

    // Calibers Management
    Route::resource('calibers', CaliberController::class)->except(['show']);
    Route::post('calibers/{caliber}/toggle-status', [CaliberController::class, 'toggleStatus'])->name('calibers.toggle-status');

    // Categories Management
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Expense Types Management
    Route::resource('expense-types', ExpenseTypeController::class)->except(['show']);

    // Users Management
    Route::resource('users', UserController::class);

    // System Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // Legacy routes for existing template compatibility
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third');
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second');
    Route::get('{any}', [RoutingController::class, 'root'])->name('any');
});
