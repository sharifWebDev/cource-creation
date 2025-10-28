<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CourseController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// Route::post('/admin/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/v1/user', function (Request $request) {
    return '$request->user()';
});

Route::middleware('auth:sanctum', 'verified')
    ->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

        //courses routes
        Route::prefix('courses')->name('api.courses.')
        ->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index');
            Route::get('/{id}', [CourseController::class, 'show'])->name('show');
            Route::delete('destroy/{id}', [CourseController::class, 'destroy'])->name('destroy');
        });

        // path3

    });
