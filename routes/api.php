<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SaleController as ApiSaleController;
use App\Http\Controllers\Api\ExpenseController as ApiExpenseController;
use App\Http\Controllers\Api\ReportController as ApiReportController;
use App\Http\Controllers\Api\BranchController as ApiBranchController;
use App\Http\Controllers\Api\EmployeeController as ApiEmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Sales API
    Route::apiResource('sales', ApiSaleController::class)->names([
        'index' => 'api.sales.index',
        'store' => 'api.sales.store',
        'show' => 'api.sales.show',
        'update' => 'api.sales.update',
        'destroy' => 'api.sales.destroy',
    ]);
    Route::post('sales/{sale}/return', [ApiSaleController::class, 'returnSale'])->name('api.sales.return');
    Route::get('sales/search/invoice', [ApiSaleController::class, 'searchByInvoice'])->name('api.sales.search-invoice');
    
    // Expenses API
    Route::apiResource('expenses', ApiExpenseController::class)->names([
        'index' => 'api.expenses.index',
        'store' => 'api.expenses.store',
        'show' => 'api.expenses.show',
        'update' => 'api.expenses.update',
        'destroy' => 'api.expenses.destroy',
    ]);
    
    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('comprehensive', [ApiReportController::class, 'comprehensive']);
        Route::get('detailed', [ApiReportController::class, 'detailed']);
        Route::get('calibers', [ApiReportController::class, 'calibers']);
        Route::get('categories', [ApiReportController::class, 'categories']);
        Route::get('employees', [ApiReportController::class, 'employees']);
        Route::get('net-profit', [ApiReportController::class, 'netProfit']);
        Route::get('dashboard-stats', [ApiReportController::class, 'dashboardStats']);
    });
    
    // Branches API
    Route::apiResource('branches', ApiBranchController::class)->names([
        'index' => 'api.branches.index',
        'store' => 'api.branches.store',
        'show' => 'api.branches.show',
        'update' => 'api.branches.update',
        'destroy' => 'api.branches.destroy',
    ]);
    Route::post('branches/{branch}/toggle-status', [ApiBranchController::class, 'toggleStatus'])->name('api.branches.toggle-status');
    
    // Employees API
    Route::apiResource('employees', ApiEmployeeController::class)->names([
        'index' => 'api.employees.index',
        'store' => 'api.employees.store',
        'show' => 'api.employees.show',
        'update' => 'api.employees.update',
        'destroy' => 'api.employees.destroy',
    ]);
    Route::post('employees/{employee}/toggle-status', [ApiEmployeeController::class, 'toggleStatus'])->name('api.employees.toggle-status');
    Route::get('branches/{branch}/employees', [ApiEmployeeController::class, 'getByBranch'])->name('api.branches.employees');
    
    // Master data API
    Route::get('master-data', function () {
        return response()->json([
            'branches' => \App\Models\Branch::active()->get(),
            'categories' => \App\Models\Category::active()->get(),
            'calibers' => \App\Models\Caliber::active()->get(),
            'expense_types' => \App\Models\ExpenseType::active()->get(),
        ]);
    });
    
    // Legacy user route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
