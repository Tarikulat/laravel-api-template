<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\PermissionController;

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

    // Role
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    // Permissions
    Route::prefix('permissions')->group(function () {
        Route::controller(PermissionController::class)->group(function () {
            Route::get('/',        'index');
            Route::post('/',       'store');
            Route::get('/{id}',    'show');
            Route::put('/{id}',    'update');
            Route::delete('/{id}', 'destroy');
        });
    });

    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/',                        'index');
            Route::post('/',                       'store');
            Route::get('/permission',              'userPermission');
            Route::get('/approval',                'approvalsList');
            Route::put('/approvals-update',        'approvalUpdate');
            Route::get('/reset-password-request',  'resetPasswordRequestList');
            Route::post('/reset-password-approve', 'resetPasswordApproval');
            Route::get('/{id}',                    'show');
            Route::put('/{id}',                    'update');
            Route::delete('/{id}',                 'destroy');
        });
    });

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
