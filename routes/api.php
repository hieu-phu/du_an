<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('locations')->group(function () {
        Route::get('/provinces', [\App\Http\Controllers\API\LocationController::class, 'provinces']);
        Route::get('/wards/{provinceId}', [\App\Http\Controllers\API\LocationController::class, 'wards']);
    });
});
