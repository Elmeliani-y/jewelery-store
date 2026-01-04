
<?php

use App\Http\Controllers\Auth\PasswordCodeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// Password code-based recovery
Route::get('/e9f3g7h2', [PasswordCodeController::class, 'showCodeForm'])->middleware('guest')->name('i5j1k6l9.m3n8o2p7');
Route::post('/e9f3g7h2/q4r9s1t6', [PasswordCodeController::class, 'requestCode'])->middleware('guest')->name('i5j1k6l9.q4r9s1t6');
Route::post('/e9f3g7h2/u2v8w3x7', [PasswordCodeController::class, 'verifyCode'])->middleware('guest')->name('i5j1k6l9.u2v8w3x7');
Route::post('/e9f3g7h2/y1z6a4b9', [PasswordCodeController::class, 'resetPassword'])->middleware('guest')->name('i5j1k6l9.y1z6a4b9');

Route::get('/c8d2e7f1', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('c8d2e7f1');

Route::post('/c8d2e7f1', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::get('/k2m7n3p8', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/k2m7n3p8', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');
