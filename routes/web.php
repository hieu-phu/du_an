<?php

use App\Http\Controllers\WEB\ActivityLogController;
use App\Http\Controllers\WEB\AttendanceController;
use App\Http\Controllers\WEB\DepartmentApprovalController;
use App\Http\Controllers\WEB\DepartmentController;
use App\Http\Controllers\WEB\PortalController;
use App\Http\Controllers\WEB\PositionController;
use App\Http\Controllers\WEB\ProjectController;
use App\Http\Controllers\WEB\UserApprovalController;
use App\Http\Controllers\WEB\UserController;
use App\Support\PositionCapability;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['auth', 'activity.log'])->group(function () {
    Route::get('/', [PortalController::class, 'dashboard']);
    Route::get('/dashboard', [PortalController::class, 'dashboard'])->name('dashboard');

    Route::get('/my-profile', [PortalController::class, 'myProfile'])
        ->middleware('access:admin,hr,employee')
        ->name('my-profile');

    Route::put('/my-profile', [PortalController::class, 'updateProfile'])
        ->middleware('access:admin,hr,employee')
        ->name('my-profile.update');

    Route::post('/my-profile/avatar', [PortalController::class, 'updateAvatar'])
        ->middleware('access:admin,hr,employee')
        ->name('my-profile.avatar');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->middleware('access:admin,hr,employee')
        ->name('attendance.check-in');

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->middleware('access:admin,hr,employee')
        ->name('attendance.check-out');

    Route::post('/attendance/requests', [AttendanceController::class, 'submitRequest'])
        ->middleware('access:admin,hr,employee')
        ->name('attendance.requests.store');

    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])
        ->middleware('access:admin,hr,employee')
        ->name('attendance.mine');

    Route::get('/my-projects', [ProjectController::class, 'myProjects'])
        ->middleware('access:admin,hr,employee')
        ->name('projects.mine');

    Route::middleware(['access:admin,hr'])->group(function () {
        Route::get('/attendance/approvals', [AttendanceController::class, 'approvals'])
            ->name('attendance.approvals');

        Route::post('/attendance/{attendanceRecord}/confirm', [AttendanceController::class, 'confirm'])
            ->name('attendance.confirm');

        Route::post('/attendance/{attendanceRecord}/reject', [AttendanceController::class, 'reject'])
            ->name('attendance.reject');

        Route::get('/attendance/reports', [AttendanceController::class, 'reports'])
            ->name('attendance.reports');

        Route::get('/attendance/reports/export/excel', [AttendanceController::class, 'exportExcel'])
            ->name('attendance.reports.export.excel');

        Route::get('/attendance/reports/export/pdf', [AttendanceController::class, 'exportPdf'])
            ->name('attendance.reports.export.pdf');

        Route::get('/projects', [ProjectController::class, 'index'])
            ->middleware('position.capability:' . PositionCapability::VIEW_ALL_PROJECTS)
            ->name('projects.index');

        Route::put('/departments/{department}/toggle', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle');
        Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'update']);
    });

    Route::middleware(['access:admin,hr'])->prefix('users')->name('web.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/employees', [UserController::class, 'employees'])->name('employees');
        Route::get('/employee-requests', [UserController::class, 'employeeRequests'])->name('employee-requests');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::put('/{user}/toggle', [UserController::class, 'toggleStatus'])->name('toggle');
        Route::put('/{user}/account-status', [UserController::class, 'updateAccountStatus'])->name('account-status');

        Route::put('/{user}/employment-status', [UserController::class, 'updateEmploymentStatus'])
            ->middleware('position.capability:' . PositionCapability::TRANSFER_EMPLOYEE)
            ->name('employment-status');
    });

    Route::middleware(['access:admin'])->group(function () {
        Route::post('/attendance/month-locks/lock', [AttendanceController::class, 'lockMonth'])
            ->name('attendance.month-locks.lock');

        Route::post('/attendance/month-locks/unlock', [AttendanceController::class, 'unlockMonth'])
            ->name('attendance.month-locks.unlock');

        Route::prefix('users/approvals')->name('web.user-approvals.')->group(function () {
            Route::get('/', [UserApprovalController::class, 'index'])->name('index');
            Route::post('/{approvalRequest}/approve', [UserApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approvalRequest}/reject', [UserApprovalController::class, 'reject'])->name('reject');
        });

        Route::prefix('departments/approvals')->name('web.department-approvals.')->group(function () {
            Route::get('/', [DepartmentApprovalController::class, 'index'])->name('index');
            Route::post('/{approvalRequest}/approve', [DepartmentApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approvalRequest}/reject', [DepartmentApprovalController::class, 'reject'])->name('reject');
        });

        Route::resource('positions', PositionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::put('/positions/{position}/toggle', [PositionController::class, 'toggleStatus'])->name('positions.toggle');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('position.capability:' . PositionCapability::VIEW_ACTIVITY_LOGS)
            ->name('activity-logs.index');
        Route::get('/settings', fn () => Inertia::render('Settings/Index'))->name('settings.index');
    });
});

require __DIR__ . '/auth.php';
