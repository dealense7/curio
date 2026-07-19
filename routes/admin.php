<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Country\CountryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        Route::prefix('countries')->group(function () {
            Route::get('/', [CountryController::class, 'index'])->name('countries.index');
            Route::post('/', [CountryController::class, 'store'])->name('countries.store');
            Route::get('{countryPublicId}', [CountryController::class, 'show'])->whereUlid('countryPublicId')->name('countries.show');
            Route::put('{countryPublicId}', [CountryController::class, 'update'])->whereUlid('countryPublicId')->name('countries.update');
            Route::delete('{countryPublicId}', [CountryController::class, 'destroy'])->whereUlid('countryPublicId')->name('countries.destroy');
        });

    });
