<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\EmployeeWorkShiftAssignment;
use App\Models\Holiday;
use App\Models\WorkShift;
use App\Models\WorkShiftOvertimeRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceCatalogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Attendance/Catalogs', [
            'workShifts' => WorkShift::query()
                ->with('overtimeRule')
                ->orderBy('shift_name')
                ->get()
                ->map(fn (WorkShift $shift) => [
                    'id' => $shift->id,
                    'shift_code' => $shift->shift_code,
                    'shift_name' => $shift->shift_name,
                    'start_time' => $this->formatTime($shift->start_time),
                    'end_time' => $this->formatTime($shift->end_time),
                    'break_start_time' => $this->formatTime($shift->break_start_time),
                    'break_end_time' => $this->formatTime($shift->break_end_time),
                    'overtime_start_time' => $this->formatTime($shift->overtimeRule?->start_time),
                    'overtime_end_time' => $this->formatTime($shift->overtimeRule?->end_time),
                    'standard_minutes' => (int) $shift->standard_minutes,
                    'half_day_minutes' => (int) $shift->half_day_minutes,
                    'handover_break_minutes' => (int) ($shift->handover_break_minutes ?? 0),
                    'overtime_hourly_rate' => $shift->overtimeRule?->hourly_rate !== null ? (float) $shift->overtimeRule->hourly_rate : null,
                    'grace_minutes' => (int) $shift->grace_minutes,
                    'late_grace_minutes' => (int) $shift->late_grace_minutes,
                    'early_leave_grace_minutes' => (int) $shift->early_leave_grace_minutes,
                    'allows_overtime' => (bool) $shift->allows_overtime,
                    'is_overnight' => (bool) $shift->is_overnight,
                    'description' => $shift->description,
                    'is_active' => (bool) $shift->is_active,
                ]),
            'holidays' => Holiday::query()
                ->orderByDesc('holiday_date')
                ->get()
                ->map(fn (Holiday $holiday) => [
                    'id' => $holiday->id,
                    'holiday_date' => optional($holiday->holiday_date)->format('Y-m-d'),
                    'holiday_name' => $holiday->holiday_name,
                    'holiday_type' => $holiday->holiday_type,
                    'is_paid_leave' => (bool) $holiday->is_paid_leave,
                    'is_recurring' => (bool) $holiday->is_recurring,
                    'note' => $holiday->note,
                ]),
            'assignments' => EmployeeWorkShiftAssignment::query()
                ->with(['employeeProfile.user:id,name', 'department:id,name', 'workShift:id,shift_name,shift_code,is_active'])
                ->orderByDesc('id')
                ->limit(200)
                ->get()
                ->map(fn (EmployeeWorkShiftAssignment $assignment) => [
                    'id' => $assignment->id,
                    'target_type' => $assignment->employee_profile_id
                        ? 'employee'
                        : ($assignment->department_id ? 'department' : 'company'),
                    'employee_profile_id' => $assignment->employee_profile_id,
                    'employee_name' => $assignment->employeeProfile?->user?->name,
                    'department_id' => $assignment->department_id,
                    'department_name' => $assignment->department?->name,
                    'work_shift_id' => $assignment->work_shift_id,
                    'work_shift_name' => $assignment->workShift?->shift_name,
                    'work_shift_code' => $assignment->workShift?->shift_code,
                    'work_shift_active' => (bool) $assignment->workShift?->is_active,
                    'effective_from' => optional($assignment->effective_from)->format('Y-m-d'),
                    'effective_to' => optional($assignment->effective_to)->format('Y-m-d'),
                    'weekdays' => $assignment->weekdays ?: [],
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
        $validated = $this->validateWorkShift($request);

        $workShift = WorkShift::query()->create($this->workShiftPayload($validated));
        $this->syncOvertimeRule($workShift, $validated);

        return back()->with('success', 'Đã tạo ca làm việc.');
    }

    public function updateWorkShift(Request $request, WorkShift $workShift)
    {
        $validated = $this->validateWorkShift($request, $workShift);

        if (($validated['is_active'] ?? true) === false && $this->hasActiveAssignments($workShift)) {
            throw ValidationException::withMessages([
                'is_active' => 'Ca này đang được phân cho nhân viên/phòng ban, không thể ngừng khi phân ca còn hiệu lực.',
            ]);
        }

        $workShift->update($this->workShiftPayload($validated, $workShift));
        $this->syncOvertimeRule($workShift, $validated);

        return back()->with('success', 'Đã cập nhật ca làm việc.');
    }

    public function toggleWorkShift(WorkShift $workShift)
    {
        if ($workShift->is_active && $this->hasActiveAssignments($workShift)) {
            return back()->withErrors([
                'work_shift' => 'Ca này đang được phân cho nhân viên/phòng ban, không thể ngừng khi phân ca còn hiệu lực.',
            ]);
        }

        $workShift->update([
            'is_active' => !$workShift->is_active,
        ]);

        return back()->with('success', 'Đã đổi trạng thái ca làm.');
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date'],
            'holiday_name' => ['required', 'string', 'max:255'],
            'holiday_type' => ['required', Rule::in(['public', 'company', 'compensatory', 'special'])],
            'is_paid_leave' => ['nullable', 'boolean'],
            'is_recurring' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        Holiday::query()->create($this->holidayPayload($validated));

        return back()->with('success', 'Đã thêm ngày nghỉ.');
    }

    public function updateHoliday(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date,' . $holiday->id],
            'holiday_name' => ['required', 'string', 'max:255'],
            'holiday_type' => ['required', Rule::in(['public', 'company', 'compensatory', 'special'])],
            'is_paid_leave' => ['nullable', 'boolean'],
            'is_recurring' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $holiday->update($this->holidayPayload($validated));

        return back()->with('success', 'Đã cập nhật ngày nghỉ.');
    }

    public function destroyHoliday(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Đã xóa ngày nghỉ.');
    }

    public function storeAssignment(Request $request)
    {
        $validated = $this->validateAssignment($request);
        $this->ensureAssignmentDoesNotOverlap($validated);

        EmployeeWorkShiftAssignment::query()->create([
            ...$this->assignmentPayload($validated),
            'created_by' => $request->user()?->id,
        ]);

        return back()->with('success', 'Đã tạo phân ca.');
    }

    public function updateAssignment(Request $request, EmployeeWorkShiftAssignment $assignment)
    {
        $validated = $this->validateAssignment($request);
        $this->ensureAssignmentDoesNotOverlap($validated, $assignment);

        $assignment->update($this->assignmentPayload($validated));

        return back()->with('success', 'Đã cập nhật phân ca.');
    }

    public function toggleAssignment(EmployeeWorkShiftAssignment $assignment)
    {
        if (!$assignment->is_active) {
            $payload = [
                'employee_profile_id' => $assignment->employee_profile_id,
                'department_id' => $assignment->department_id,
                'effective_from' => optional($assignment->effective_from)->format('Y-m-d'),
                'effective_to' => optional($assignment->effective_to)->format('Y-m-d'),
            ];

            $this->ensureAssignmentDoesNotOverlap($payload, $assignment);
        }

        $assignment->update([
            'is_active' => !$assignment->is_active,
        ]);

        return back()->with('success', 'Đã đổi trạng thái phân ca.');
    }

    private function validateWorkShift(Request $request, ?WorkShift $workShift = null): array
    {
        $validated = $request->validate([
            'shift_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('work_shifts', 'shift_code')->ignore($workShift?->id),
            ],
            'shift_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('work_shifts', 'shift_name')->ignore($workShift?->id),
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_start_time' => ['nullable', 'date_format:H:i'],
            'break_end_time' => ['nullable', 'date_format:H:i'],
            'overtime_start_time' => ['nullable', 'date_format:H:i'],
            'overtime_end_time' => ['nullable', 'date_format:H:i'],
            'standard_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'half_day_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'handover_break_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'overtime_hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'late_grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'early_leave_grace_minutes' => ['nullable', 'integer', 'min:0', 'max:180'],
            'allows_overtime' => ['nullable', 'boolean'],
            'is_overnight' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $breakStart = $validated['break_start_time'] ?? null;
        $breakEnd = $validated['break_end_time'] ?? null;

        if (filled($breakStart) xor filled($breakEnd)) {
            throw ValidationException::withMessages([
                filled($breakStart) ? 'break_end_time' : 'break_start_time' => 'Cần nhập đủ cả giờ bắt đầu và kết thúc nghỉ giữa ca.',
            ]);
        }

        $overtimeStart = $validated['overtime_start_time'] ?? null;
        $overtimeEnd = $validated['overtime_end_time'] ?? null;
        if (filled($overtimeStart) xor filled($overtimeEnd)) {
            throw ValidationException::withMessages([
                filled($overtimeStart) ? 'overtime_end_time' : 'overtime_start_time' => 'Cần nhập đủ giờ bắt đầu và kết thúc tăng ca.',
            ]);
        }

        $workMinutes = $this->timeRangeMinutes(
            $validated['start_time'],
            $validated['end_time'],
            (bool) ($validated['is_overnight'] ?? false)
        );

        if ($workMinutes <= 0) {
            throw ValidationException::withMessages([
                'end_time' => 'Giờ kết thúc phải sau giờ bắt đầu, hoặc bật ca qua đêm.',
            ]);
        }

        $breakMinutes = 0;
        if (filled($breakStart) && filled($breakEnd)) {
            $breakMinutes = $this->timeRangeMinutes($breakStart, $breakEnd, (bool) ($validated['is_overnight'] ?? false));

            if (
                $breakMinutes <= 0 ||
                $breakMinutes >= $workMinutes ||
                !$this->timeRangeInsideShift($validated['start_time'], $validated['end_time'], $breakStart, $breakEnd, (bool) ($validated['is_overnight'] ?? false))
            ) {
                throw ValidationException::withMessages([
                    'break_end_time' => 'Khung giờ nghỉ giữa ca phải nằm trong thời lượng ca làm.',
                ]);
            }
        }

        $netMinutes = $workMinutes - $breakMinutes;
        if ($netMinutes <= 0) {
            throw ValidationException::withMessages([
                'break_end_time' => 'Tổng thời gian nghỉ giữa ca không được bằng hoặc lớn hơn tổng thời gian ca làm.',
            ]);
        }

        if ((int) $validated['standard_minutes'] !== $netMinutes) {
            throw ValidationException::withMessages([
                'standard_minutes' => 'Phút chuẩn phải khớp với thời lượng ca sau khi trừ nghỉ giữa ca (' . $netMinutes . ' phút).',
            ]);
        }

        if ((int) $validated['half_day_minutes'] > (int) $validated['standard_minutes']) {
            throw ValidationException::withMessages([
                'half_day_minutes' => 'Ngưỡng nửa công không được lớn hơn phút chuẩn.',
            ]);
        }

        if (!($validated['allows_overtime'] ?? true) && (filled($overtimeStart) || filled($overtimeEnd) || filled($validated['overtime_hourly_rate'] ?? null))) {
            throw ValidationException::withMessages([
                'allows_overtime' => 'Hãy bật tính tăng ca hoặc xóa cấu hình quy tắc tăng ca.',
            ]);
        }

        if (filled($overtimeStart) && filled($overtimeEnd)) {
            $shiftStartMinutes = $this->minutesOfDay($validated['start_time']);
            $shiftEndMinutes = $this->minutesOfDay($validated['end_time']);
            $overtimeStartMinutes = $this->minutesOfDay($overtimeStart);

            if ((bool) ($validated['is_overnight'] ?? false)) {
                if ($shiftEndMinutes <= $shiftStartMinutes) {
                    $shiftEndMinutes += 1440;
                }

                if ($overtimeStartMinutes < $shiftStartMinutes) {
                    $overtimeStartMinutes += 1440;
                }
            }

            if ($overtimeStartMinutes !== $shiftEndMinutes) {
                $gapStart = substr($validated['end_time'], 0, 5);
                $gapEnd = substr($overtimeStart, 0, 5);

                throw ValidationException::withMessages([
                    'overtime_start_time' => $overtimeStartMinutes > $shiftEndMinutes
                        ? 'Không được để khoảng hở giữa giờ hành chính và tăng ca (' . $gapStart . '-' . $gapEnd . ').'
                        : 'Giờ bắt đầu tăng ca phải nối tiếp ngay sau giờ kết thúc ca.',
                ]);
            }
        }

        return $validated;
    }

    private function workShiftPayload(array $validated, ?WorkShift $workShift = null): array
    {
        return [
            'shift_code' => $workShift?->shift_code ?: $this->generateShiftCode(),
            'shift_name' => trim($validated['shift_name']),
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'break_start_time' => filled($validated['break_start_time'] ?? null) ? $validated['break_start_time'] . ':00' : null,
            'break_end_time' => filled($validated['break_end_time'] ?? null) ? $validated['break_end_time'] . ':00' : null,
            'standard_minutes' => (int) $validated['standard_minutes'],
            'half_day_minutes' => (int) $validated['half_day_minutes'],
            'handover_break_minutes' => (int) ($validated['handover_break_minutes'] ?? 0),
            'grace_minutes' => (int) ($validated['grace_minutes'] ?? 0),
            'late_grace_minutes' => (int) ($validated['late_grace_minutes'] ?? 0),
            'early_leave_grace_minutes' => (int) ($validated['early_leave_grace_minutes'] ?? 0),
            'allows_overtime' => (bool) ($validated['allows_overtime'] ?? true),
            'is_overnight' => (bool) ($validated['is_overnight'] ?? false),
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ];
    }

    private function syncOvertimeRule(WorkShift $workShift, array $validated): void
    {
        $startTime = filled($validated['overtime_start_time'] ?? null) ? $validated['overtime_start_time'] . ':00' : null;
        $endTime = filled($validated['overtime_end_time'] ?? null) ? $validated['overtime_end_time'] . ':00' : null;
        $hourlyRate = filled($validated['overtime_hourly_rate'] ?? null) ? (float) $validated['overtime_hourly_rate'] : null;

        if (!$startTime && !$endTime && $hourlyRate === null) {
            $workShift->overtimeRule()->delete();

            return;
        }

        WorkShiftOvertimeRule::query()->updateOrCreate(
            ['work_shift_id' => $workShift->id],
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hourly_rate' => $hourlyRate,
            ]
        );
    }

    private function generateShiftCode(): string
    {
        $lastNumber = WorkShift::query()
            ->whereNotNull('shift_code')
            ->pluck('shift_code')
            ->map(function (?string $code) {
                if (!$code || !preg_match('/(\d+)$/', $code, $matches)) {
                    return null;
                }

                return (int) $matches[1];
            })
            ->filter(fn (?int $number) => $number !== null)
            ->max();

        $nextNumber = ((int) $lastNumber) + 1;

        return 'CA' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function holidayPayload(array $validated): array
    {
        return [
            'holiday_date' => $validated['holiday_date'],
            'holiday_name' => trim($validated['holiday_name']),
            'holiday_type' => $validated['holiday_type'],
            'is_paid_leave' => (bool) ($validated['is_paid_leave'] ?? true),
            'is_recurring' => (bool) ($validated['is_recurring'] ?? false),
            'note' => $validated['note'] ?? null,
        ];
    }

    private function validateAssignment(Request $request): array
    {
        $validated = $request->validate([
            'target_type' => ['required', Rule::in(['employee', 'department', 'company'])],
            'employee_profile_id' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'work_shift_id' => ['required', 'integer', 'exists:work_shifts,id'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'weekdays' => ['nullable', 'array'],
            'weekdays.*' => ['integer', 'min:1', 'max:7'],
            'note' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($validated['target_type'] === 'employee') {
            if (!filled($validated['employee_profile_id'] ?? null)) {
                throw ValidationException::withMessages([
                    'employee_profile_id' => 'Cần chọn nhân viên để phân ca.',
                ]);
            }
            $validated['department_id'] = null;
        }

        if ($validated['target_type'] === 'department') {
            if (!filled($validated['department_id'] ?? null)) {
                throw ValidationException::withMessages([
                    'department_id' => 'Cần chọn phòng ban để phân ca.',
                ]);
            }
            $validated['employee_profile_id'] = null;
        }

        if ($validated['target_type'] === 'company') {
            $validated['employee_profile_id'] = null;
            $validated['department_id'] = null;
        }

        $shiftIsActive = WorkShift::query()
            ->whereKey($validated['work_shift_id'])
            ->where('is_active', true)
            ->exists();

        if (!$shiftIsActive) {
            throw ValidationException::withMessages([
                'work_shift_id' => 'Chỉ được phân ca đang hoạt động.',
            ]);
        }

        return $validated;
    }

    private function assignmentPayload(array $validated): array
    {
        $weekdays = collect($validated['weekdays'] ?? [])
            ->map(fn ($day) => (int) $day)
            ->filter(fn ($day) => $day >= 1 && $day <= 7)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [
            'employee_profile_id' => $validated['employee_profile_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'work_shift_id' => $validated['work_shift_id'],
            'effective_from' => $validated['effective_from'],
            'effective_to' => $validated['effective_to'] ?? null,
            'weekdays' => $weekdays ?: null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'note' => $validated['note'] ?? null,
        ];
    }

    private function ensureAssignmentDoesNotOverlap(array $validated, ?EmployeeWorkShiftAssignment $current = null): void
    {
        $from = $validated['effective_from'];
        $to = $validated['effective_to'] ?? '9999-12-31';
        $query = EmployeeWorkShiftAssignment::query()
            ->where('is_active', true)
            ->when($current, fn ($builder) => $builder->whereKeyNot($current->id))
            ->whereDate('effective_from', '<=', $to)
            ->where(function ($builder) use ($from) {
                $builder->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $from);
            });

        if (filled($validated['employee_profile_id'] ?? null)) {
            $overlaps = $query
                ->where('employee_profile_id', $validated['employee_profile_id'])
                ->exists();

            if ($overlaps) {
                throw ValidationException::withMessages([
                    'employee_profile_id' => 'Nhân viên này đã có phân ca hiệu lực trong khoảng ngày được chọn.',
                ]);
            }

            return;
        }

        if (filled($validated['department_id'] ?? null)) {
            $overlaps = $query
                ->whereNull('employee_profile_id')
                ->where('department_id', $validated['department_id'])
                ->exists();

            if ($overlaps) {
                throw ValidationException::withMessages([
                    'department_id' => 'Phòng ban này đã có phân ca hiệu lực trong khoảng ngày được chọn.',
                ]);
            }

            return;
        }

        $overlaps = $query
            ->whereNull('employee_profile_id')
            ->whereNull('department_id')
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages([
                'target_type' => 'Đã tồn tại phân ca toàn công ty hiệu lực trong khoảng ngày được chọn.',
            ]);
        }
    }

    private function hasActiveAssignments(WorkShift $workShift): bool
    {
        return $workShift->employeeAssignments()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', now()->toDateString());
            })
            ->exists();
    }

    private function timeRangeMinutes(string $start, string $end, bool $overnight): int
    {
        $startMinutes = $this->minutesOfDay($start);
        $endMinutes = $this->minutesOfDay($end);

        if ($overnight && $endMinutes <= $startMinutes) {
            $endMinutes += 1440;
        }

        return $endMinutes - $startMinutes;
    }

    private function minutesOfDay(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return $hour * 60 + $minute;
    }

    private function timeRangeInsideShift(string $shiftStart, string $shiftEnd, string $rangeStart, string $rangeEnd, bool $overnight): bool
    {
        $shiftStartMinutes = $this->minutesOfDay($shiftStart);
        $shiftEndMinutes = $this->minutesOfDay($shiftEnd);
        $rangeStartMinutes = $this->minutesOfDay($rangeStart);
        $rangeEndMinutes = $this->minutesOfDay($rangeEnd);

        if ($overnight) {
            if ($shiftEndMinutes <= $shiftStartMinutes) {
                $shiftEndMinutes += 1440;
            }
            if ($rangeStartMinutes < $shiftStartMinutes) {
                $rangeStartMinutes += 1440;
            }
            if ($rangeEndMinutes <= $rangeStartMinutes) {
                $rangeEndMinutes += 1440;
            }
        }

        return $rangeStartMinutes >= $shiftStartMinutes && $rangeEndMinutes <= $shiftEndMinutes && $rangeEndMinutes > $rangeStartMinutes;
    }

    private function formatTime(?string $time): ?string
    {
        if (!filled($time)) {
            return null;
        }

        return substr($time, 0, 5);
    }
}


