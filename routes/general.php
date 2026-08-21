<?php

declare(strict_types=1);

use App\Http\Controllers\Company\CompanySettingController;
use App\Http\Controllers\General\Country\CountryController;
use App\Http\Controllers\General\Tour\TourController;
use Illuminate\Support\Facades\Route;

Route::prefix('general')->name('general.')->group(function (): void {
    Route::get('countries', [CountryController::class, 'index'])->name('countries.index');

    Route::prefix('tours')->group(function (): void {
        Route::get('config', [TourController::class, 'config'])->name('tours.config');
        Route::get('/', [TourController::class, 'index'])->name('tours.index');
        Route::get('{tourPublicId}', [TourController::class, 'show'])->whereUlid('tourPublicId')->name('tours.show');
    });
});

Route::middleware('auth:api')
    ->prefix('company')
    ->name('company.')
    ->group(function (): void {
        Route::get('settings', [CompanySettingController::class, 'show'])->name('settings.show');
        Route::patch('settings', [CompanySettingController::class, 'update'])->name('settings.update');
    });
