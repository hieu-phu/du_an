<?php

use App\Http\Controllers\WEB\ActivityLogController;
use App\Http\Controllers\WEB\AttendanceController;
use App\Http\Controllers\WEB\AttendanceAdjustmentController;
use App\Http\Controllers\WEB\AttendanceCatalogController;
use App\Http\Controllers\WEB\DepartmentApprovalController;
use App\Http\Controllers\WEB\DepartmentController;
use App\Http\Controllers\WEB\FeedbackController;
use App\Http\Controllers\WEB\PortalController;
use App\Http\Controllers\WEB\PositionController;
use App\Http\Controllers\WEB\ProjectController;
use App\Http\Controllers\WEB\ReportController;
use App\Http\Controllers\WEB\SalaryController;
use App\Http\Controllers\WEB\SettingsController;
use App\Http\Controllers\WEB\UserApprovalController;
use App\Http\Controllers\WEB\UserController;
use App\Support\PositionCapability;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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
    Route::get('/', [PortalController::class, 'dashboard'])
        ->middleware('position.capability:' . PositionCapability::VIEW_DASHBOARD);
    Route::get('/dashboard', [PortalController::class, 'dashboard'])
        ->middleware('position.capability:' . PositionCapability::VIEW_DASHBOARD)
        ->name('dashboard');

    Route::get('/my-profile', [PortalController::class, 'myProfile'])
        ->middleware('position.capability:' . PositionCapability::VIEW_OWN_PROFILE)
        ->name('my-profile');

    Route::put('/my-profile', [PortalController::class, 'updateProfile'])
        ->middleware('position.capability:' . PositionCapability::UPDATE_OWN_PROFILE)
        ->name('my-profile.update');
    Route::post('/my-profile/avatar', [PortalController::class, 'updateAvatar'])
        ->middleware('position.capability:' . PositionCapability::UPDATE_OWN_PROFILE)
        ->name('my-profile.avatar');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])
        ->middleware('position.capability:' . PositionCapability::CHECK_IN)
        ->name('attendance.check-in');

    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])
        ->middleware('position.capability:' . PositionCapability::CHECK_OUT)
        ->name('attendance.check-out');

    Route::post('/attendance/requests', [AttendanceController::class, 'submitRequest'])
        ->middleware('position.capability:' . PositionCapability::REQUEST_ATTENDANCE_ADJUSTMENT)
        ->name('attendance.requests.store');

    Route::get('/attendance/adjustments', [AttendanceAdjustmentController::class, 'index'])
        ->middleware('position.capability:' . PositionCapability::REQUEST_ATTENDANCE_ADJUSTMENT)
        ->name('attendance.adjustments.index');

    Route::post('/attendance/adjustments', [AttendanceAdjustmentController::class, 'store'])
        ->middleware('position.capability:' . PositionCapability::REQUEST_ATTENDANCE_ADJUSTMENT)
        ->name('attendance.adjustments.store');

    Route::get('/feedbacks', [FeedbackController::class, 'index'])
        ->middleware('position.capability:' . PositionCapability::VIEW_FEEDBACKS)
        ->name('feedbacks.index');

    Route::post('/feedbacks', [FeedbackController::class, 'store'])
        ->middleware('position.capability:' . PositionCapability::CREATE_FEEDBACK)
        ->name('feedbacks.store');

    Route::post('/feedbacks/{feedbackMessage}/read', [FeedbackController::class, 'markRead'])
        ->middleware('position.capability:' . PositionCapability::VIEW_FEEDBACKS)
        ->name('feedbacks.read');

    Route::post('/feedbacks/{feedbackMessage}/reply', [FeedbackController::class, 'reply'])
        ->middleware('position.capability:' . PositionCapability::VIEW_FEEDBACKS)
        ->name('feedbacks.reply');

    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])
        ->middleware('position.capability:' . PositionCapability::VIEW_OWN_ATTENDANCE)
        ->name('attendance.mine');

    Route::get('/my-salary', [SalaryController::class, 'mine'])
        ->middleware('position.capability:' . PositionCapability::VIEW_OWN_SALARY)
        ->name('salary.mine');

    Route::get('/my-projects', [ProjectController::class, 'myProjects'])
        ->middleware('position.capability:' . PositionCapability::VIEW_OWN_PROJECTS)
        ->name('projects.mine');

    Route::group([], function () {
        Route::get('/projects', [ProjectController::class, 'index'])
            ->name('projects.index');
        Route::post('/projects', [ProjectController::class, 'store'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECTS)
            ->name('projects.store');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECTS)
            ->name('projects.update');
        Route::put('/projects/{project}/toggle-lock', [ProjectController::class, 'toggleLock'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECTS)
            ->name('projects.toggle-lock');
        Route::post('/projects/{project}/members', [ProjectController::class, 'addMember'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.members.store');
        Route::put('/projects/{project}/members/{projectMember}', [ProjectController::class, 'updateMemberRole'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.members.update');
        Route::delete('/projects/{project}/members/{projectMember}', [ProjectController::class, 'removeMember'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.members.destroy');
        Route::post('/projects/{project}/roles', [ProjectController::class, 'addRole'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_ROLES)
            ->name('projects.roles.store');
        Route::delete('/projects/{project}/roles/{projectRole}', [ProjectController::class, 'removeRole'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_ROLES)
            ->name('projects.roles.destroy');
        Route::post('/projects/{project}/implementation-details', [ProjectController::class, 'storeImplementationDetail'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.implementation-details.store');
        Route::put('/projects/{project}/implementation-details/{implementationDetail}', [ProjectController::class, 'updateImplementationDetail'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.implementation-details.update');
        Route::put('/projects/{project}/implementation-details/{implementationDetail}/status', [ProjectController::class, 'updateImplementationDetailStatus'])
            ->middleware('position.capability:' . PositionCapability::UPDATE_PROJECT_TASK_STATUS)
            ->name('projects.implementation-details.status');
        Route::put('/projects/{project}/implementation-details/{implementationDetail}/toggle-lock', [ProjectController::class, 'toggleImplementationDetailLock'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.implementation-details.toggle-lock');
        Route::delete('/projects/{project}/implementation-details/{implementationDetail}', [ProjectController::class, 'destroyImplementationDetail'])
            ->middleware('position.capability:' . PositionCapability::MANAGE_PROJECT_MEMBERS)
            ->name('projects.implementation-details.destroy');
    });

    Route::middleware(['position.capability:' . PositionCapability::APPROVE_ATTENDANCE])->group(function () {
        Route::get('/attendance/approvals', [AttendanceController::class, 'approvals'])
            ->name('attendance.approvals');

        Route::post('/attendance/{attendanceRecord}/confirm', [AttendanceController::class, 'confirm'])
            ->name('attendance.confirm');

        Route::post('/attendance/{attendanceRecord}/reject', [AttendanceController::class, 'reject'])
            ->name('attendance.reject');

        Route::post('/attendance/request-approvals/{approvalRequest}/approve', [AttendanceController::class, 'approveRequest'])
            ->name('attendance.request-approvals.approve');

        Route::post('/attendance/request-approvals/{approvalRequest}/reject', [AttendanceController::class, 'rejectRequest'])
            ->name('attendance.request-approvals.reject');

        Route::get('/attendance/adjustments/approvals', [AttendanceAdjustmentController::class, 'approvals'])
            ->name('attendance.adjustments.approvals');
        Route::post('/attendance/adjustments/{adjustment}/approve', [AttendanceAdjustmentController::class, 'approve'])
            ->name('attendance.adjustments.approve');
        Route::post('/attendance/adjustments/{adjustment}/reject', [AttendanceAdjustmentController::class, 'reject'])
            ->name('attendance.adjustments.reject');
    });

    Route::middleware(['position.capability:' . PositionCapability::VIEW_ALL_ATTENDANCE])->group(function () {
        Route::get('/attendance/reports', [AttendanceController::class, 'reports'])
            ->name('attendance.reports');
    });

    Route::middleware(['position.capability:' . PositionCapability::EXPORT_ATTENDANCE])->group(function () {
        Route::get('/attendance/reports/export/excel', [AttendanceController::class, 'exportExcel'])
            ->name('attendance.reports.export.excel');

        Route::get('/attendance/reports/export/pdf', [AttendanceController::class, 'exportPdf'])
            ->name('attendance.reports.export.pdf');
    });

    Route::middleware(['position.capability:' . PositionCapability::MANAGE_DEPARTMENTS])->group(function () {
        Route::put('/departments/{department}/toggle', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle');
        Route::resource('departments', DepartmentController::class)->only(['index', 'store', 'update']);
    });

    Route::middleware(['position.capability:' . PositionCapability::MANAGE_EMPLOYEES])->prefix('users')->name('web.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/employees', [UserController::class, 'employees'])->name('employees');
        Route::get('/employee-requests', [UserController::class, 'employeeRequests'])->name('employee-requests');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::put('/{user}/toggle', [UserController::class, 'toggleStatus'])->name('toggle');
        Route::put('/{user}/account-status', [UserController::class, 'updateAccountStatus'])->name('account-status');
        Route::post('/{user}/capability-overrides', [UserController::class, 'upsertCapabilityOverride'])
            ->name('capability-overrides.upsert');
        Route::delete('/{user}/capability-overrides/{override}', [UserController::class, 'destroyCapabilityOverride'])
            ->name('capability-overrides.destroy');

        Route::put('/{user}/employment-status', [UserController::class, 'updateEmploymentStatus'])
            ->middleware('position.capability:' . PositionCapability::TRANSFER_EMPLOYEE)
            ->name('employment-status');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])
            ->middleware('position.capability:' . PositionCapability::VIEW_REPORTS)
            ->name('index');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])
            ->middleware('position.capability:' . PositionCapability::EXPORT_REPORTS)
            ->name('export.excel');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])
            ->middleware('position.capability:' . PositionCapability::EXPORT_REPORTS)
            ->name('export.pdf');
    });

    Route::middleware(['position.capability:' . PositionCapability::APPROVE_ATTENDANCE])->group(function () {
        Route::post('/attendance/month-locks/lock', [AttendanceController::class, 'lockMonth'])
            ->name('attendance.month-locks.lock');

        Route::post('/attendance/month-locks/unlock', [AttendanceController::class, 'unlockMonth'])
            ->name('attendance.month-locks.unlock');

        Route::get('/attendance/catalogs', [AttendanceCatalogController::class, 'index'])
            ->name('attendance.catalogs.index');
        Route::post('/attendance/catalogs/work-shifts', [AttendanceCatalogController::class, 'storeWorkShift'])
            ->name('attendance.catalogs.work-shifts.store');
        Route::put('/attendance/catalogs/work-shifts/{workShift}', [AttendanceCatalogController::class, 'updateWorkShift'])
            ->name('attendance.catalogs.work-shifts.update');
        Route::put('/attendance/catalogs/work-shifts/{workShift}/toggle', [AttendanceCatalogController::class, 'toggleWorkShift'])
            ->name('attendance.catalogs.work-shifts.toggle');

        Route::post('/attendance/catalogs/holidays', [AttendanceCatalogController::class, 'storeHoliday'])
            ->name('attendance.catalogs.holidays.store');
        Route::put('/attendance/catalogs/holidays/{holiday}', [AttendanceCatalogController::class, 'updateHoliday'])
            ->name('attendance.catalogs.holidays.update');
        Route::delete('/attendance/catalogs/holidays/{holiday}', [AttendanceCatalogController::class, 'destroyHoliday'])
            ->name('attendance.catalogs.holidays.destroy');

        Route::post('/attendance/catalogs/assignments', [AttendanceCatalogController::class, 'storeAssignment'])
            ->name('attendance.catalogs.assignments.store');
        Route::put('/attendance/catalogs/assignments/{assignment}', [AttendanceCatalogController::class, 'updateAssignment'])
            ->name('attendance.catalogs.assignments.update');
        Route::put('/attendance/catalogs/assignments/{assignment}/toggle', [AttendanceCatalogController::class, 'toggleAssignment'])
            ->name('attendance.catalogs.assignments.toggle');
    });

    Route::middleware(['position.capability:' . PositionCapability::APPROVE_REQUESTS])->group(function () {
        Route::prefix('users/approvals')->name('web.user-approvals.')->group(function () {
            Route::get('/', [UserApprovalController::class, 'index'])->name('index');
            Route::post('/{approvalRequest}/approve', [UserApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approvalRequest}/reject', [UserApprovalController::class, 'reject'])->name('reject');
            Route::post('/{approvalRequest}/cancel', [UserApprovalController::class, 'cancel'])->name('cancel');
        });

        Route::prefix('departments/approvals')->name('web.department-approvals.')->group(function () {
            Route::get('/', [DepartmentApprovalController::class, 'index'])->name('index');
            Route::post('/{approvalRequest}/approve', [DepartmentApprovalController::class, 'approve'])->name('approve');
            Route::post('/{approvalRequest}/reject', [DepartmentApprovalController::class, 'reject'])->name('reject');
            Route::post('/{approvalRequest}/cancel', [DepartmentApprovalController::class, 'cancel'])->name('cancel');
        });
    });

    Route::middleware(['position.capability:' . PositionCapability::MANAGE_POSITIONS])->group(function () {
        Route::resource('positions', PositionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::put('/positions/{position}/toggle', [PositionController::class, 'toggleStatus'])->name('positions.toggle');
        Route::post('/positions/capabilities', [PositionController::class, 'storeCapability'])->name('positions.capabilities.store');
        Route::post('/positions/authority-levels', [PositionController::class, 'storeAuthorityLevel'])->name('positions.authority-levels.store');
        Route::put('/positions/authority-levels/{authorityLevel}/toggle', [PositionController::class, 'toggleAuthorityLevel'])->name('positions.authority-levels.toggle');
    });

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('position.capability:' . PositionCapability::VIEW_ACTIVITY_LOGS)
        ->name('activity-logs.index');

    Route::get('/settings', [SettingsController::class, 'index'])
        ->middleware('position.capability:' . PositionCapability::VIEW_ACTIVITY_LOGS)
        ->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])
        ->middleware('position.capability:' . PositionCapability::VIEW_ACTIVITY_LOGS)
        ->name('settings.update');
});

require __DIR__ . '/auth.php';
