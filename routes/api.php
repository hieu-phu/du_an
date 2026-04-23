<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'activity.log'])->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

    Route::prefix('locations')->group(function () {
        Route::get('/provinces', [\App\Http\Controllers\API\LocationController::class, 'provinces']);
        Route::get('/wards/{provinceId}', [\App\Http\Controllers\API\LocationController::class, 'wards']);
    });
});
