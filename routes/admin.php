<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Admin\Country\CountryController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Retailer\RetailerController;
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

        Route::prefix('retailers')->group(function (): void {
            Route::get('/', [RetailerController::class, 'index'])->name('retailers.index');
            Route::post('/', [RetailerController::class, 'store'])->name('retailers.store');
            Route::get('{retailerPublicId}', [RetailerController::class, 'show'])->whereUlid('retailerPublicId')->name('retailers.show');
            Route::put('{retailerPublicId}', [RetailerController::class, 'update'])->whereUlid('retailerPublicId')->name('retailers.update');
            Route::delete('{retailerPublicId}', [RetailerController::class, 'destroy'])->whereUlid('retailerPublicId')->name('retailers.destroy');
        });

        Route::prefix('categories')->group(function (): void {
            Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('{categoryPublicId}', [CategoryController::class, 'show'])->whereUlid('categoryPublicId')->name('categories.show');
            Route::put('{categoryPublicId}', [CategoryController::class, 'update'])->whereUlid('categoryPublicId')->name('categories.update');
            Route::delete('{categoryPublicId}', [CategoryController::class, 'destroy'])->whereUlid('categoryPublicId')->name('categories.destroy');
        });

        Route::prefix('products')->group(function (): void {
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::post('/', [ProductController::class, 'store'])->name('products.store');
            Route::get('{productPublicId}', [ProductController::class, 'show'])->whereUlid('productPublicId')->name('products.show');
            Route::put('{productPublicId}', [ProductController::class, 'update'])->whereUlid('productPublicId')->name('products.update');
            Route::delete('{productPublicId}', [ProductController::class, 'destroy'])->whereUlid('productPublicId')->name('products.destroy');
        });

    });
