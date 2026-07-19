<?php

declare(strict_types=1);

use App\Http\Controllers\General\Country\CountryController;
use Illuminate\Support\Facades\Route;

Route::prefix('general')->name('general.')->group(function (): void {
    Route::get('countries', [CountryController::class, 'index'])->name('countries.index');
});
