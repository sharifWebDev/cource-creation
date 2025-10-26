<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
 

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'), 'verified',
])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/artisan/optimize', [AdminController::class, 'optimize'])->name('artisan.optimize');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/toggle-status/{id}', [AdminController::class, 'toggleStatus'])->name('data.toggleStatus');


});
