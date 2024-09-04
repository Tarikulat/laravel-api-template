<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentController;

Route::get('/clear', function () {
    Artisan::call('optimize:clear');

    return 'Success! Your are very lucky!';
});

// Auth
Route::controller(AuthController::class)->group(function () {
    Route::post('registration', 'registration');
    Route::post('reset-password', 'resetPassword');
    Route::post('login', 'login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Dashboard
    // Route::get('dashboard', [DashboardController::class, 'dashboard']);



    // route
    Route::prefix('contents')->group(function () {
        Route::controller(ContentController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('trash',                     'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });



    // Logout route
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('logout',          [AuthController::class, 'logout']);
});
