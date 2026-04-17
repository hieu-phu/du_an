<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\EmployeeWorkShiftAssignment;
use App\Models\Holiday;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceCatalogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Attendance/Catalogs', [
            'workShifts' => WorkShift::query()
                ->orderBy('shift_name')
                ->get()
                ->map(fn (WorkShift $shift) => [
                    'id' => $shift->id,
                    'shift_name' => $shift->shift_name,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'standard_minutes' => (int) $shift->standard_minutes,
                    'grace_minutes' => (int) $shift->grace_minutes,
                    'late_grace_minutes' => (int) $shift->late_grace_minutes,
                    'early_leave_grace_minutes' => (int) $shift->early_leave_grace_minutes,
                    'is_active' => (bool) $shift->is_active,
                ]),
            'holidays' => Holiday::query()
                ->orderByDesc('holiday_date')
                ->get()
                ->map(fn (Holiday $holiday) => [
                    'id' => $holiday->id,
                    'holiday_date' => optional($holiday->holiday_date)->format('Y-m-d'),
                    'holiday_name' => $holiday->holiday_name,
                    'is_paid_leave' => (bool) $holiday->is_paid_leave,
                ]),
            'assignments' => EmployeeWorkShiftAssignment::query()
                ->with(['employeeProfile.user:id,name', 'department:id,name', 'workShift:id,shift_name'])
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->map(fn (EmployeeWorkShiftAssignment $assignment) => [
                    'id' => $assignment->id,
                    'employee_profile_id' => $assignment->employee_profile_id,
                    'employee_name' => $assignment->employeeProfile?->user?->name,
                    'department_id' => $assignment->department_id,
                    'department_name' => $assignment->department?->name,
                    'work_shift_id' => $assignment->work_shift_id,
                    'work_shift_name' => $assignment->workShift?->shift_name,
                    'effective_from' => optional($assignment->effective_from)->format('Y-m-d'),
                    'effective_to' => optional($assignment->effective_to)->format('Y-m-d'),
                    'is_active' => (bool) $assignment->is_active,
                    'note' => $assignment->note,
                ]),
            'employeeOptions' => EmployeeProfile::query()
                ->with('user:id,name')
                ->orderBy('employee_code')
                ->get()
                ->map(fn (EmployeeProfile $profile) => [
                    'id' => $profile->id,
                    'label' => trim(($profile->employee_code ?: 'EMP') . ' - ' . ($profile->user?->name ?: 'Unknown')),
                ]),
            'departmentOptions' => Department::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Department $department) => [
                    'id' => $department->id,
                    'name' => $department->name,
                ]),
        ]);
    }

    public function storeWorkShift(Request $request)
    {
        $validated = $request->validate([
            'shift_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'standard_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'late_grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'early_leave_grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        WorkShift::query()->create([
            'shift_name' => $validated['shift_name'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'standard_minutes' => (int) $validated['standard_minutes'],
            'grace_minutes' => (int) ($validated['grace_minutes'] ?? 0),
            'late_grace_minutes' => (int) ($validated['late_grace_minutes'] ?? 0),
            'early_leave_grace_minutes' => (int) ($validated['early_leave_grace_minutes'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return back()->with('success', 'Da tao ca lam viec.');
    }

    public function updateWorkShift(Request $request, WorkShift $workShift)
    {
        $validated = $request->validate([
            'shift_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'standard_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'late_grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'early_leave_grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $workShift->update([
            'shift_name' => $validated['shift_name'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'standard_minutes' => (int) $validated['standard_minutes'],
            'grace_minutes' => (int) ($validated['grace_minutes'] ?? 0),
            'late_grace_minutes' => (int) ($validated['late_grace_minutes'] ?? 0),
            'early_leave_grace_minutes' => (int) ($validated['early_leave_grace_minutes'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? $workShift->is_active),
        ]);

        return back()->with('success', 'Da cap nhat ca lam viec.');
    }

    public function toggleWorkShift(WorkShift $workShift)
    {
        $workShift->update([
            'is_active' => !$workShift->is_active,
        ]);

        return back()->with('success', 'Da doi trang thai ca lam.');
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date'],
            'holiday_name' => ['required', 'string', 'max:255'],
            'is_paid_leave' => ['nullable', 'boolean'],
        ]);

        Holiday::query()->create([
            'holiday_date' => $validated['holiday_date'],
            'holiday_name' => $validated['holiday_name'],
            'is_paid_leave' => (bool) ($validated['is_paid_leave'] ?? true),
        ]);

        return back()->with('success', 'Da them ngay nghi.');
    }

    public function updateHoliday(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date,' . $holiday->id],
            'holiday_name' => ['required', 'string', 'max:255'],
            'is_paid_leave' => ['nullable', 'boolean'],
        ]);

        $holiday->update([
            'holiday_date' => $validated['holiday_date'],
            'holiday_name' => $validated['holiday_name'],
            'is_paid_leave' => (bool) ($validated['is_paid_leave'] ?? true),
        ]);

        return back()->with('success', 'Da cap nhat ngay nghi.');
    }

    public function destroyHoliday(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Da xoa ngay nghi.');
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'employee_profile_id' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'work_shift_id' => ['required', 'integer', 'exists:work_shifts,id'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'note' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!filled($validated['employee_profile_id'] ?? null) && !filled($validated['department_id'] ?? null)) {
            return back()->withErrors([
                'assignment' => 'Can chon nhan vien hoac phong ban de phan ca.',
            ]);
        }

        EmployeeWorkShiftAssignment::query()->create([
            'employee_profile_id' => $validated['employee_profile_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'work_shift_id' => $validated['work_shift_id'],
            'effective_from' => $validated['effective_from'],
            'effective_to' => $validated['effective_to'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'note' => $validated['note'] ?? null,
            'created_by' => $request->user()?->id,
        ]);

        return back()->with('success', 'Da tao phan ca.');
    }

    public function updateAssignment(Request $request, EmployeeWorkShiftAssignment $assignment)
    {
        $validated = $request->validate([
            'employee_profile_id' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'work_shift_id' => ['required', 'integer', 'exists:work_shifts,id'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'note' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!filled($validated['employee_profile_id'] ?? null) && !filled($validated['department_id'] ?? null)) {
            return back()->withErrors([
                'assignment' => 'Can chon nhan vien hoac phong ban de phan ca.',
            ]);
        }

        $assignment->update([
            'employee_profile_id' => $validated['employee_profile_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'work_shift_id' => $validated['work_shift_id'],
            'effective_from' => $validated['effective_from'],
            'effective_to' => $validated['effective_to'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? $assignment->is_active),
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('success', 'Da cap nhat phan ca.');
    }

    public function toggleAssignment(EmployeeWorkShiftAssignment $assignment)
    {
        $assignment->update([
            'is_active' => !$assignment->is_active,
        ]);

        return back()->with('success', 'Da doi trang thai phan ca.');
    }
}

