<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\DjController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\PhotographerController;
use App\Http\Controllers\PhotographerPhotoController;
use App\Http\Controllers\PresenterController;
use App\Http\Controllers\RentalCarController;
use App\Http\Controllers\VideographerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('photographers', PhotographerController::class)->only(['index', 'show']);
Route::apiResource('videographers', VideographerController::class)->only(['index', 'show']);
Route::apiResource('bands', BandController::class)->only(['index', 'show']);
Route::apiResource('djs', DjController::class)->only(['index', 'show']);
Route::apiResource('presenters', PresenterController::class)->only(['index', 'show']);
Route::apiResource('halls', HallController::class)->only(['index', 'show']);
Route::apiResource('rental-cars', RentalCarController::class)
    ->parameters(['rental-cars' => 'rentalCar'])
    ->only(['index', 'show']);

Route::prefix('admin')->group(function (): void {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::apiResource('photographers', PhotographerController::class)->only(['store', 'update', 'destroy']);
        Route::delete(
            '/photographers/{photographer}/photos/{photo}',
            [PhotographerPhotoController::class, 'destroy']
        )->scopeBindings();
        Route::apiResource('videographers', VideographerController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('bands', BandController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('djs', DjController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('presenters', PresenterController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('halls', HallController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('rental-cars', RentalCarController::class)
            ->parameters(['rental-cars' => 'rentalCar'])
            ->only(['store', 'update', 'destroy']);
    });
});
