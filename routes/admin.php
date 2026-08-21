<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\Country\CountryController;
use App\Http\Controllers\Admin\Tour\TourController;
use App\Http\Controllers\Company\CompanySettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')
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

        Route::prefix('companies')->group(function (): void {
            Route::get('{companyPublicId}/settings', [CompanySettingController::class, 'show'])->whereUlid('companyPublicId')->name('companies.settings.show');
            Route::patch('{companyPublicId}/settings', [CompanySettingController::class, 'update'])->whereUlid('companyPublicId')->name('companies.settings.update');
            Route::get('config', [CompanyController::class, 'config'])->name('companies.config');
            Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
            Route::post('/', [CompanyController::class, 'store'])->name('companies.store');
            Route::get('{companyPublicId}', [CompanyController::class, 'show'])->whereUlid('companyPublicId')->name('companies.show');
            Route::post('{companyPublicId}/suspend', [CompanyController::class, 'suspend'])->whereUlid('companyPublicId')->name('companies.suspend');
            Route::post('{companyPublicId}/reactivate', [CompanyController::class, 'reactivate'])->whereUlid('companyPublicId')->name('companies.reactivate');
            Route::post('{companyPublicId}/archive', [CompanyController::class, 'archive'])->whereUlid('companyPublicId')->name('companies.archive');
        });

        Route::prefix('tours')->group(function (): void {
            Route::get('/', [TourController::class, 'index'])->name('tours.index');
            Route::post('/', [TourController::class, 'store'])->name('tours.store');
            Route::get('{tourPublicId}', [TourController::class, 'show'])->whereUlid('tourPublicId')->name('tours.show');
            Route::put('{tourPublicId}', [TourController::class, 'update'])->whereUlid('tourPublicId')->name('tours.update');
            Route::delete('{tourPublicId}', [TourController::class, 'destroy'])->whereUlid('tourPublicId')->name('tours.destroy');
        });

    });
