<?php

use App\Http\Controllers\WEB\UserController;
use App\Http\Controllers\WEB\DepartmentController;
use App\Http\Controllers\WEB\PositionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Setup routes (chỉ cho phép ở môi trường local/staging) ──────────────────
if (app()->isLocal() || app()->environment('staging')) {
    Route::get('/setup', function () {
        $request = request();
        if ($request->get('fresh')) {
            Artisan::call('migrate:fresh');
            Artisan::call('db:seed');
        } else {
            Artisan::call('migrate');
        }
        return 'Database migrated and seeded!';
    });
    Route::get('/setup-s', function () {
        Artisan::call('storage:link');
        return 'Storage linked!';
    });
}
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('DashBoard');
    });
    Route::get('/dashboard', function () {
        return Inertia::render('DashBoard');
    })->name('dashboard');
    Route::prefix('users')->name('web.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::put('/{user}/toggle', [UserController::class, 'toggleStatus'])->name('toggle');
    });

    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
});
require __DIR__ . '/auth.php';
