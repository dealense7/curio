<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->group(static function (): void {
        Route::post('token', [AuthController::class, 'token'])
            ->middleware('throttle:auth-token')
            ->name('token');

        Route::middleware('auth:api')->group(static function (): void {
            Route::get('me', [AuthController::class, 'currentUser'])->name('me');
            Route::get('acl', [AuthController::class, 'permissions'])->name('acl');
            Route::delete('token', [AuthController::class, 'revokeToken'])->name('revoke-token');
        });
    });
