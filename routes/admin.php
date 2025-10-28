<?php

// routes/admin.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// path1

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/artisan/optimize', [AdminController::class, 'optimize'])->name('artisan.optimize');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/toggle-status/{id}', [AdminController::class, 'toggleStatus'])->name('data.toggleStatus');


    Route::prefix('/courses')
        ->controller(CourseController::class)
        ->group(function () {
            Route::get('/', 'index')->name('courses.index');
            Route::get('/create', 'create')->name('courses.create');
            Route::post('/', 'store')->name('courses.store');
            Route::get('/{customer_detail}', 'show')->name('courses.show');
            Route::get('/{customer_detail}/edit', 'edit')->name('courses.edit');
            Route::put('/{customer_detail}', 'update')->name('courses.update');
            Route::delete('/{customer_detail}', 'destroy')->name('courses.destroy');
        });
    Route::post('/{id}/publish', [CourseController::class, 'publish'])->name('publish');
    Route::post('/{id}/unpublish', [CourseController::class, 'unpublish'])->name('unpublish');

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // path3

});
