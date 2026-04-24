<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAdjustment;
use App\Models\AttendanceRecord;
use App\Models\EmployeeProfile;
use App\Models\EmployeeWorkShiftAssignment;
use App\Models\Holiday;
use App\Models\OvertimeRequest;
use App\Models\PayrollPeriod;
use App\Models\SalaryAdjustment;
use App\Models\SalaryHistory;
use App\Models\SalarySnapshot;
use App\Support\PositionCapability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SalaryController extends Controller
{
    private const TIMEZONE = 'Asia/Ho_Chi_Minh';
    private const DEFAULT_FULL_DAY_MINUTES = 480;
    private const DEFAULT_HALF_DAY_MINUTES = 240;
    private const OVERTIME_WEEKDAY_MULTIPLIER = 1.5;
    private const OVERTIME_WEEKEND_MULTIPLIER = 2.0;
    private const OVERTIME_HOLIDAY_MULTIPLIER = 3.0;

    public function mine(Request $request): Response
    {
        $payload = $this->buildMySalaryPayload($request);
        $salaryData = $payload['salaryData'];

        return Inertia::render('Salary/My', [
            'filters' => $this->buildFilterOptions($payload['month'], $payload['year'], $payload['now']),
            'profile' => $salaryData['profile'],
            'summary' => $salaryData['summary'],
            'records' => $salaryData['records'],
            'salaryHistory' => $salaryData['salaryHistory'],
            'periodStatus' => $this->periodStatusPayload($payload['payrollPeriod'], $payload['month'], $payload['year']),
        ]);
    }

    public function exportMinePdf(Request $request)
    {
        $payload = $this->buildMySalaryPayload($request);
        $salaryData = $payload['salaryData'];
        $profile = $salaryData['profile'];
        $month = (int) $payload['month'];
        $year = (int) $payload['year'];
        $employeeCode = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($profile['employee_code'] ?? 'employee'));
        $filename = sprintf('payslip-%s-%04d-%02d.pdf', trim($employeeCode, '-') ?: 'employee', $year, $month);

        $html = view('exports.my-salary-pdf', [
            'filters' => ['month' => $month, 'year' => $year],
            'profile' => $profile,
            'summary' => $salaryData['summary'],
            'records' => $salaryData['records'],
            'adjustments' => $salaryData['adjustments'] ?? collect(),
            'periodStatus' => $this->periodStatusPayload($payload['payrollPeriod'], $month, $year),
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function company(Request $request): Response
    {
        $payload = $this->buildCompanySalaryPayload($request, true);

        return Inertia::render('Salary/Company', [
            'filters' => $payload['filters'],
            'departments' => $payload['departments'],
            'positions' => $payload['positions'],
            'periodStatus' => $payload['periodStatus'],
            'permissions' => $payload['permissions'],
            'summary' => $payload['summary'],
            'rows' => $payload['rows'],
            'selectedDetail' => $payload['selectedDetail'],
        ]);
    }

    public function exportCompanyExcel(Request $request)
    {
        $payload = $this->buildCompanySalaryPayload($request, false);
        $month = (int) $payload['filters']['month'];
        $year = (int) $payload['filters']['year'];
        $filename = sprintf('company-salary-%04d-%02d.xls', $year, $month);

        $content = view('exports.company-salary-excel', [
            'filters' => $payload['filters'],
            'summary' => $payload['summary'],
            'rows' => $payload['rows'],
        ])->render();

        return response("\xEF\xBB\xBF" . $content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function lockCompanyPeriod(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPositionCapability(PositionCapability::MANAGE_SALARY), 403);

        [$month, $year] = $this->resolvePeriod($request);
        $this->refreshSnapshots($request, $month, $year, true);

        return redirect()->back()->with('success', 'Da chot ky luong va luu snapshot.');
    }

    public function unlockCompanyPeriod(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPositionCapability(PositionCapability::MANAGE_SALARY), 403);

        [$month, $year] = $this->resolvePeriod($request);
        $period = PayrollPeriod::query()->where(['month' => $month, 'year' => $year])->first();

        if ($period) {
            $period->update([
                'status' => 'draft',
                'unlocked_at' => now(self::TIMEZONE),
                'unlocked_by' => $request->user()->id,
            ]);
        }

        return redirect()->back()->with('success', 'Da mo khoa ky luong. Du lieu quay ve che do tinh dong.');
    }

    public function recalculateCompanyPeriod(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPositionCapability(PositionCapability::MANAGE_SALARY), 403);

        [$month, $year] = $this->resolvePeriod($request);
        $period = $this->payrollPeriod($month, $year);

        if (!$period || $period->status !== 'locked') {
            return redirect()->back()->with('warning', 'Ky luong chua khoa. He thong dang tinh luong dong, khong can tinh lai snapshot.');
        }

        $this->refreshSnapshots($request, $month, $year, true);

        return redirect()->back()->with('success', 'Da tinh lai snapshot ky luong.');
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPositionCapability(PositionCapability::MANAGE_SALARY), 403);

        $validated = $request->validate([
            'employee_profile_id' => ['required', 'integer', 'exists:employee_profiles,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'type' => ['required', 'in:allowance,deduction'],
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($this->lockedPayrollPeriod((int) $validated['month'], (int) $validated['year'])) {
            return redirect()->back()->with('warning', 'Ky luong da khoa. Vui long mo khoa ky luong truoc khi them phu cap hoac khau tru.');
        }

        SalaryAdjustment::query()->create([
            'employee_profile_id' => $validated['employee_profile_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'type' => $validated['type'],
            'label' => trim($validated['label']),
            'amount' => $validated['amount'],
            'note' => filled($validated['note'] ?? null) ? trim($validated['note']) : null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Da luu khoan dieu chinh luong.');
    }

    public function destroyAdjustment(Request $request, SalaryAdjustment $salaryAdjustment): RedirectResponse
    {
        abort_unless($request->user()->hasPositionCapability(PositionCapability::MANAGE_SALARY), 403);

        $month = (int) $salaryAdjustment->month;
        $year = (int) $salaryAdjustment->year;

        if ($this->lockedPayrollPeriod($month, $year)) {
            return redirect()->back()->with('warning', 'Ky luong da khoa. Vui long mo khoa ky luong truoc khi xoa phu cap hoac khau tru.');
        }

        $salaryAdjustment->delete();

        return redirect()->back()->with('success', 'Da xoa khoan dieu chinh luong.');
    }

    private function buildMySalaryPayload(Request $request): array
    {
        [$month, $year, $now] = $this->resolvePeriod($request);

        $profile = $request->user()
            ->employeeProfile()
            ->with(['user:id,name,email', 'department:id,name', 'position:id,name'])
            ->firstOrFail();

        $payrollPeriod = $this->payrollPeriod($month, $year);
        $lockedPeriod = $payrollPeriod && $payrollPeriod->status === 'locked' ? $payrollPeriod : null;
        $salaryData = $lockedPeriod
            ? $this->buildSalaryStatementFromSnapshot(
                $lockedPeriod->snapshots()->where('employee_profile_id', $profile->id)->first(),
                $profile,
                $month,
                $year
            )
            : $this->buildSalaryStatement($profile, null, $month, $year, true);

        return [
            'month' => $month,
            'year' => $year,
            'now' => $now,
            'profile' => $profile,
            'payrollPeriod' => $payrollPeriod,
            'salaryData' => $salaryData,
        ];
    }

    private function resolvePeriod(Request $request): array
    {
        $now = Carbon::now(self::TIMEZONE);
        $month = (int) $request->integer('month', $now->month);
        $year = (int) $request->integer('year', $now->year);

        if ($month < 1 || $month > 12) {
            $month = (int) $now->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = (int) $now->year;
        }

        return [$month, $year, $now];
    }

    private function buildFilterOptions(int $month, int $year, Carbon $now): array
    {
        return [
            'month' => $month,
            'year' => $year,
            'months' => collect(range(1, 12))
                ->map(fn (int $item) => [
                    'value' => $item,
                    'label' => 'Thang ' . str_pad((string) $item, 2, '0', STR_PAD_LEFT),
                ])
                ->values(),
            'years' => collect(range($now->year - 3, $now->year + 1))
                ->map(fn (int $item) => [
                    'value' => $item,
                    'label' => (string) $item,
                ])
                ->values(),
        ];
    }

    private function buildSalaryStatement(EmployeeProfile $profile, $records = null, int $month = null, int $year = null, bool $includeSalaryHistory = true): array
    {
        $month ??= (int) now(self::TIMEZONE)->month;
        $year ??= (int) now(self::TIMEZONE)->year;

        $records ??= AttendanceRecord::query()
            ->with([
                'workShift:id,shift_name,start_time,end_time,break_start_time,break_end_time,standard_minutes,half_day_minutes,handover_break_minutes,grace_minutes,late_grace_minutes,early_leave_grace_minutes,is_overnight',
                'workShift.overtimeRule:id,work_shift_id,hourly_rate',
            ])
            ->where('employee_profile_id', $profile->id)
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year)
            ->orderBy('work_date')
            ->get();

        $baseSalary = (float) ($profile->base_salary ?? 0);
        $holidayMap = $this->holidayMap($month, $year);
        $shiftAssignments = $this->shiftAssignmentsForPeriod($profile, $month, $year);
        $expectedWorkDays = $this->countExpectedWorkDays($profile, $month, $year, $holidayMap, $shiftAssignments);
        $dailyRate = $expectedWorkDays > 0 ? $baseSalary / $expectedWorkDays : 0.0;
        $hourlyRate = $dailyRate / 8;
        $paidHolidayRows = $this->buildPaidHolidayRows($profile, $records, $holidayMap, $dailyRate, $shiftAssignments);
        $paidHolidayWorkUnits = (float) $paidHolidayRows->sum('work_unit');

        $approvedRecords = $records->filter(fn (AttendanceRecord $record) => $this->isEffectivelyApproved($record));
        $pendingRecords = $records->filter(fn (AttendanceRecord $record) => !$this->isEffectivelyApproved($record) && ($record->approval_status ?? 'pending') === 'pending');

        $approvedWorkUnits = (float) $approvedRecords->sum(fn (AttendanceRecord $record) => $this->resolvePayableWorkUnit($record)) + $paidHolidayWorkUnits;
        $pendingWorkUnits = (float) $pendingRecords->sum(fn (AttendanceRecord $record) => $this->resolvePayableWorkUnit($record));
        $approvedOvertimeMinutes = (int) $records->sum(fn (AttendanceRecord $record) => $this->resolveApprovedOvertimeMinutes($record));
        $payableWorkUnits = min($approvedWorkUnits, (float) $expectedWorkDays);
        $unpaidWorkUnits = max(0.0, (float) $expectedWorkDays - $approvedWorkUnits - $pendingWorkUnits);
        $baseSalaryAmount = $dailyRate * $payableWorkUnits;
        $pendingAmount = $dailyRate * $pendingWorkUnits;
        $attendanceDeductionAmount = $dailyRate * $unpaidWorkUnits;
        $overtimeAmount = $records->sum(fn (AttendanceRecord $record) => $this->resolveOvertimeAmount($record, $hourlyRate, $holidayMap));
        $adjustments = $this->salaryAdjustments($profile->id, $month, $year);
        $allowanceAmount = (float) $adjustments->where('type', 'allowance')->sum('amount');
        $manualDeductionAmount = (float) $adjustments->where('type', 'deduction')->sum('amount');
        $deductionAmount = $attendanceDeductionAmount + $manualDeductionAmount;
        $grossAmount = $baseSalaryAmount + $overtimeAmount + $allowanceAmount;
        $netAmount = max(0, $grossAmount - $manualDeductionAmount);
        $warnings = $this->buildSalaryWarnings($profile, $records, $approvedRecords, $pendingRecords, $baseSalary);

        $salaryHistory = collect();
        if ($includeSalaryHistory) {
            $salaryHistory = SalaryHistory::query()
                ->where('employee_profile_id', $profile->id)
                ->orderByDesc('effective_date')
                ->limit(10)
                ->get()
                ->map(fn (SalaryHistory $history) => [
                    'id' => $history->id,
                    'old_salary' => (float) $history->old_salary,
                    'new_salary' => (float) $history->new_salary,
                    'currency' => $history->currency ?: 'VND',
                    'effective_date' => optional($history->effective_date)->format('Y-m-d'),
                    'note' => $history->note,
                ])
                ->values();
        }

        return [
            'profile' => [
                'id' => $profile->id,
                'employee_code' => $profile->employee_code,
                'name' => $profile->user?->name,
                'email' => $profile->user?->email,
                'department' => $profile->department?->name,
                'position' => $profile->position?->name,
                'employment_type' => $profile->employment_type,
                'currency' => $profile->salary_currency ?: 'VND',
            ],
            'summary' => [
                'base_salary' => round($baseSalary, 2),
                'expected_work_days' => $expectedWorkDays,
                'daily_rate' => round($dailyRate, 2),
                'hourly_rate' => round($hourlyRate, 2),
                'approved_work_units' => round($approvedWorkUnits, 2),
                'pending_work_units' => round($pendingWorkUnits, 2),
                'unpaid_work_units' => round($unpaidWorkUnits, 2),
                'approved_overtime_minutes' => $approvedOvertimeMinutes,
                'base_salary_amount' => round($baseSalaryAmount, 2),
                'overtime_amount' => round($overtimeAmount, 2),
                'allowance_amount' => round($allowanceAmount, 2),
                'pending_amount' => round($pendingAmount, 2),
                'attendance_deduction_amount' => round($attendanceDeductionAmount, 2),
                'manual_deduction_amount' => round($manualDeductionAmount, 2),
                'deduction_amount' => round($deductionAmount, 2),
                'gross_amount' => round($grossAmount, 2),
                'net_amount' => round($netAmount, 2),
                'total_records' => $records->count(),
                'approved_records' => $approvedRecords->count(),
                'pending_records' => $pendingRecords->count(),
                'rejected_records' => $records->where('approval_status', 'rejected')->count(),
                'warning_count' => $warnings->count(),
                'warnings' => $warnings->values(),
            ],
            'records' => $records
                ->map(fn (AttendanceRecord $record) => [
                    'id' => $record->id,
                    'work_date' => optional($record->work_date)->format('Y-m-d'),
                    'check_in_at' => $record->check_in_at ? Carbon::parse($record->check_in_at, self::TIMEZONE)->format('H:i') : null,
                    'check_out_at' => $record->check_out_at ? Carbon::parse($record->check_out_at, self::TIMEZONE)->format('H:i') : null,
                    'shift_name' => $this->resolveShiftName($record),
                    'shift_time_range' => $this->resolveShiftTimeRange($record),
                    'shift_standard_minutes' => $this->resolveShiftStandardMinutes($record),
                    'shift_half_day_minutes' => $this->resolveShiftHalfDayMinutes($record),
                    'worked_minutes' => $this->resolveWorkedMinutes($record),
                    'work_unit' => $this->resolvePayableWorkUnit($record),
                    'payable_amount' => round($dailyRate * $this->resolvePayableWorkUnit($record), 2),
                    'overtime_minutes' => $this->resolveApprovedOvertimeMinutes($record),
                    'overtime_multiplier' => $this->resolveOvertimeMultiplier($record, $holidayMap),
                    'overtime_hourly_rate' => $this->resolveOvertimeHourlyRate($record),
                    'overtime_rate_source' => $this->resolveOvertimeHourlyRate($record) > 0 ? 'catalog' : 'multiplier',
                    'overtime_type_label' => $this->resolveOvertimeTypeLabel($record, $holidayMap),
                    'overtime_amount' => round($this->resolveOvertimeAmount($record, $hourlyRate, $holidayMap), 2),
                    'attendance_status' => $this->resolvePayableAttendanceStatus($record),
                    'day_status' => $this->resolvePayableDayStatus($record),
                    'approval_status' => $this->resolveApprovalStatus($record),
                    'approval_note' => $record->approval_note,
                ])
                ->concat($paidHolidayRows)
                ->sortBy('work_date')
                ->values(),
            'adjustments' => $adjustments
                ->map(fn (SalaryAdjustment $adjustment) => [
                    'id' => $adjustment->id,
                    'type' => $adjustment->type,
                    'label' => $adjustment->label,
                    'amount' => round((float) $adjustment->amount, 2),
                    'note' => $adjustment->note,
                ])
                ->values(),
            'salaryHistory' => $salaryHistory,
        ];
    }

    private function buildSalaryStatementFromSnapshot(?SalarySnapshot $snapshot, EmployeeProfile $profile, int $month, int $year): array
    {
        if ($snapshot && is_array($snapshot->payload)) {
            $payload = $snapshot->payload;
            $payload['profile']['id'] ??= $profile->id;
            $payload['adjustments'] ??= [];
            $payload['summary']['allowance_amount'] ??= 0;
            $payload['summary']['attendance_deduction_amount'] ??= $payload['summary']['deduction_amount'] ?? 0;
            $payload['summary']['manual_deduction_amount'] ??= 0;
            $payload['summary']['warning_count'] ??= count($payload['summary']['warnings'] ?? []);
            $payload['summary']['warnings'] ??= [];

            return $payload;
        }

        return $this->buildSalaryStatement($profile, null, $month, $year, true);
    }

    private function sortCompanyRows(Collection $rows, string $sortBy, string $sortDir): Collection
    {
        $allowedSorts = [
            'employee_code',
            'name',
            'department',
            'position',
            'base_salary',
            'approved_work_units',
            'approved_overtime_minutes',
            'overtime_amount',
            'allowance_amount',
            'pending_amount',
            'attendance_deduction_amount',
            'manual_deduction_amount',
            'deduction_amount',
            'net_amount',
            'warning_count',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'employee_code';
        }

        $resolver = function (array $row) use ($sortBy) {
            return match ($sortBy) {
                'name' => mb_strtolower((string) ($row['name'] ?? '')),
                'department' => mb_strtolower((string) ($row['department'] ?? '')),
                'position' => mb_strtolower((string) ($row['position'] ?? '')),
                'base_salary' => (float) ($row['summary']['base_salary'] ?? 0),
                'approved_work_units' => (float) ($row['summary']['approved_work_units'] ?? 0),
                'approved_overtime_minutes' => (int) ($row['summary']['approved_overtime_minutes'] ?? 0),
                'overtime_amount' => (float) ($row['summary']['overtime_amount'] ?? 0),
                'allowance_amount' => (float) ($row['summary']['allowance_amount'] ?? 0),
                'pending_amount' => (float) ($row['summary']['pending_amount'] ?? 0),
                'attendance_deduction_amount' => (float) ($row['summary']['attendance_deduction_amount'] ?? 0),
                'manual_deduction_amount' => (float) ($row['summary']['manual_deduction_amount'] ?? 0),
                'deduction_amount' => (float) ($row['summary']['deduction_amount'] ?? 0),
                'net_amount' => (float) ($row['summary']['net_amount'] ?? 0),
                'warning_count' => (int) ($row['summary']['warning_count'] ?? 0),
                default => mb_strtolower((string) ($row['employee_code'] ?? '')),
            };
        };

        $sorted = $rows->sortBy($resolver, options: SORT_NATURAL | SORT_FLAG_CASE);

        return $sortDir === 'desc' ? $sorted->reverse()->values() : $sorted->values();
    }

    private function paginateCollection(Collection $items, int $perPage, int $page, Request $request): LengthAwarePaginator
    {
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function buildCompanySalaryPayload(Request $request, bool $paginate, bool $preferLockedSnapshot = true): array
    {
        [$month, $year, $now] = $this->resolvePeriod($request);
        $keyword = trim((string) $request->string('keyword'));
        $departmentId = $request->integer('department_id') ?: null;
        $positionId = $request->integer('position_id') ?: null;
        $sortBy = (string) $request->string('sort_by', 'employee_code');
        $sortDir = strtolower((string) $request->string('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(50, (int) $request->integer('per_page', 10)));
        $page = max(1, (int) $request->integer('page', 1));
        $selectedProfileId = $request->integer('employee_profile_id') ?: null;
        $payrollPeriod = $this->payrollPeriod($month, $year);
        $period = $preferLockedSnapshot && $payrollPeriod && $payrollPeriod->status === 'locked'
            ? $payrollPeriod
            : null;

        $profilesQuery = EmployeeProfile::query()
            ->with(['user:id,name,email,status', 'department:id,name', 'position:id,name'])
            ->whereIn('employment_status', ['active', 'probation'])
            ->whereHas('user', fn ($query) => $query->where('status', 'active'));

        if ($keyword !== '') {
            $profilesQuery->where(function ($query) use ($keyword) {
                $query
                    ->where('employee_code', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('department', fn ($departmentQuery) => $departmentQuery->where('name', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('position', fn ($positionQuery) => $positionQuery->where('name', 'like', '%' . $keyword . '%'));
            });
        }

        if ($departmentId) {
            $profilesQuery->where('department_id', $departmentId);
        }

        if ($positionId) {
            $profilesQuery->where('position_id', $positionId);
        }

        $profiles = $profilesQuery
            ->orderBy('employee_code')
            ->get();

        $snapshotMap = collect();
        $recordsByProfile = collect();
        if ($period) {
            $snapshotMap = $period->snapshots()
                ->whereIn('employee_profile_id', $profiles->pluck('id'))
                ->get()
                ->keyBy('employee_profile_id');
        } else {
            $recordsByProfile = AttendanceRecord::query()
                ->with([
                    'workShift:id,shift_name,start_time,end_time,break_start_time,break_end_time,standard_minutes,half_day_minutes,handover_break_minutes,grace_minutes,late_grace_minutes,early_leave_grace_minutes,is_overnight',
                    'workShift.overtimeRule:id,work_shift_id,hourly_rate',
                ])
                ->whereIn('employee_profile_id', $profiles->pluck('id'))
                ->whereMonth('work_date', $month)
                ->whereYear('work_date', $year)
                ->orderBy('work_date')
                ->get()
                ->groupBy('employee_profile_id');
        }

        $allSalaryData = $profiles
            ->map(function (EmployeeProfile $profile) use ($recordsByProfile, $snapshotMap, $period, $month, $year) {
                $salaryData = $period
                    ? $this->buildSalaryStatementFromSnapshot($snapshotMap->get($profile->id), $profile, $month, $year)
                    : $this->buildSalaryStatement($profile, $recordsByProfile->get($profile->id), $month, $year, false);

                return [
                    'employee_profile_id' => $profile->id,
                    'salary_data' => $salaryData,
                ];
            });

        $rows = $allSalaryData
            ->map(function (array $item) {
                $salaryData = $item['salary_data'];

                return [
                    'employee_profile_id' => $item['employee_profile_id'],
                    'employee_code' => $salaryData['profile']['employee_code'],
                    'name' => $salaryData['profile']['name'],
                    'email' => $salaryData['profile']['email'],
                    'department' => $salaryData['profile']['department'],
                    'position' => $salaryData['profile']['position'],
                    'employment_type' => $salaryData['profile']['employment_type'],
                    'currency' => $salaryData['profile']['currency'],
                    'summary' => $salaryData['summary'],
                    'warnings' => $salaryData['summary']['warnings'] ?? [],
                ];
            });

        $rows = $this->sortCompanyRows($rows, $sortBy, $sortDir)->values();

        $selectedDetail = null;
        if ($paginate && $selectedProfileId) {
            $selectedData = $allSalaryData->firstWhere('employee_profile_id', $selectedProfileId);
            if ($selectedData) {
                $selectedDetail = $selectedData['salary_data'];
            }
        }

        $departments = EmployeeProfile::query()
            ->with('department:id,name')
            ->whereNotNull('department_id')
            ->whereIn('employment_status', ['active', 'probation'])
            ->get()
            ->pluck('department')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $positions = EmployeeProfile::query()
            ->with('position:id,name')
            ->whereNotNull('position_id')
            ->whereIn('employment_status', ['active', 'probation'])
            ->get()
            ->pluck('position')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return [
            'filters' => array_merge($this->buildFilterOptions($month, $year, $now), [
                'keyword' => $keyword,
                'department_id' => $departmentId,
                'position_id' => $positionId,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
                'page' => $page,
                'employee_profile_id' => $selectedProfileId,
            ]),
            'periodStatus' => $this->periodStatusPayload($payrollPeriod, $month, $year),
            'permissions' => [
                'can_manage_payroll' => $request->user()->hasPositionCapability(PositionCapability::MANAGE_SALARY),
            ],
            'departments' => $departments,
            'positions' => $positions,
            'summary' => [
                'employee_count' => $rows->count(),
                'total_base_salary' => round((float) $rows->sum(fn (array $row) => $row['summary']['base_salary']), 2),
                'total_base_salary_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['base_salary_amount']), 2),
                'total_overtime_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['overtime_amount']), 2),
                'total_allowance_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['allowance_amount'] ?? 0), 2),
                'total_gross_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['gross_amount'] ?? 0), 2),
                'total_pending_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['pending_amount']), 2),
                'total_attendance_deduction_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['attendance_deduction_amount'] ?? 0), 2),
                'total_manual_deduction_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['manual_deduction_amount'] ?? 0), 2),
                'total_deduction_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['deduction_amount'] ?? 0), 2),
                'total_net_amount' => round((float) $rows->sum(fn (array $row) => $row['summary']['net_amount']), 2),
                'total_approved_overtime_minutes' => (int) $rows->sum(fn (array $row) => $row['summary']['approved_overtime_minutes']),
                'warning_employee_count' => (int) $rows->filter(fn (array $row) => (int) ($row['summary']['warning_count'] ?? 0) > 0)->count(),
                'total_warning_count' => (int) $rows->sum(fn (array $row) => $row['summary']['warning_count'] ?? 0),
            ],
            'rows' => $paginate ? $this->paginateCollection($rows, $perPage, $page, $request) : $rows->values(),
            'selectedDetail' => $selectedDetail,
            'allSalaryData' => $allSalaryData,
        ];
    }

    private function payrollPeriod(int $month, int $year): ?PayrollPeriod
    {
        return PayrollPeriod::query()
            ->with(['locker:id,name', 'snapshots'])
            ->where('month', $month)
            ->where('year', $year)
            ->first();
    }

    private function lockedPayrollPeriod(int $month, int $year): ?PayrollPeriod
    {
        $period = $this->payrollPeriod($month, $year);

        return $period && $period->status === 'locked' ? $period : null;
    }

    private function periodStatusPayload(?PayrollPeriod $period, int $month, int $year): array
    {
        if (!$period) {
            return [
                'id' => null,
                'status' => 'draft',
                'status_label' => 'Nhap / tinh dong',
                'month' => $month,
                'year' => $year,
                'is_locked' => false,
                'locked_at' => null,
                'locked_by_name' => null,
                'snapshot_count' => 0,
            ];
        }

        return [
            'id' => $period->id,
            'status' => $period->status,
            'status_label' => $period->status === 'locked' ? 'Da khoa snapshot' : 'Nhap / tinh dong',
            'month' => $month,
            'year' => $year,
            'is_locked' => $period->status === 'locked',
            'locked_at' => optional($period->locked_at)->setTimezone(self::TIMEZONE)->format('Y-m-d H:i:s'),
            'locked_by_name' => $period->locker?->name,
            'snapshot_count' => $period->snapshots->count(),
        ];
    }

    private function refreshSnapshots(Request $request, int $month, int $year, bool $lockPeriod = true): PayrollPeriod
    {
        return DB::transaction(function () use ($request, $month, $year, $lockPeriod) {
            $payload = $this->buildCompanySalaryPayload($request, false, false);

            $period = PayrollPeriod::query()->updateOrCreate(
                ['month' => $month, 'year' => $year],
                [
                    'status' => $lockPeriod ? 'locked' : 'draft',
                    'locked_at' => $lockPeriod ? now(self::TIMEZONE) : null,
                    'locked_by' => $lockPeriod ? $request->user()->id : null,
                    'unlocked_at' => null,
                    'unlocked_by' => null,
                ]
            );

            $snapshotIds = [];
            foreach ($payload['allSalaryData'] as $item) {
                $salaryData = $item['salary_data'];
                $summary = $salaryData['summary'];

                $snapshot = SalarySnapshot::query()->updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'employee_profile_id' => $item['employee_profile_id'],
                    ],
                    [
                        'employee_code' => $salaryData['profile']['employee_code'] ?? null,
                        'employee_name' => $salaryData['profile']['name'] ?? null,
                        'department_name' => $salaryData['profile']['department'] ?? null,
                        'position_name' => $salaryData['profile']['position'] ?? null,
                        'currency' => $salaryData['profile']['currency'] ?? 'VND',
                        'base_salary' => $summary['base_salary'] ?? 0,
                        'approved_work_units' => $summary['approved_work_units'] ?? 0,
                        'approved_overtime_minutes' => $summary['approved_overtime_minutes'] ?? 0,
                        'base_salary_amount' => $summary['base_salary_amount'] ?? 0,
                        'overtime_amount' => $summary['overtime_amount'] ?? 0,
                        'allowance_amount' => $summary['allowance_amount'] ?? 0,
                        'pending_amount' => $summary['pending_amount'] ?? 0,
                        'attendance_deduction_amount' => $summary['attendance_deduction_amount'] ?? 0,
                        'manual_deduction_amount' => $summary['manual_deduction_amount'] ?? 0,
                        'deduction_amount' => $summary['deduction_amount'] ?? 0,
                        'net_amount' => $summary['net_amount'] ?? 0,
                        'warning_count' => $summary['warning_count'] ?? 0,
                        'payload' => $salaryData,
                    ]
                );

                $snapshotIds[] = $snapshot->id;
            }

            $staleSnapshots = SalarySnapshot::query()->where('payroll_period_id', $period->id);
            if ($snapshotIds !== []) {
                $staleSnapshots->whereNotIn('id', $snapshotIds);
            }

            $staleSnapshots->delete();

            return $period->fresh(['locker:id,name', 'snapshots']);
        });
    }

    private function salaryAdjustments(int $employeeProfileId, int $month, int $year): Collection
    {
        return SalaryAdjustment::query()
            ->where('employee_profile_id', $employeeProfileId)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('type')
            ->orderBy('label')
            ->get();
    }

    private function buildSalaryWarnings(
        EmployeeProfile $profile,
        Collection $records,
        Collection $approvedRecords,
        Collection $pendingRecords,
        float $baseSalary
    ): Collection {
        $warnings = collect();

        if ($baseSalary <= 0) {
            $warnings->push('Chua khai bao luong co ban.');
        }

        if ($records->isEmpty()) {
            $warnings->push('Chua co du lieu cham cong trong ky.');
        }

        $missingCount = $records->filter(fn (AttendanceRecord $record) => (bool) $record->missing_check_in || (bool) $record->missing_check_out || ($record->check_in_at && !$record->check_out_at))->count();
        if ($missingCount > 0) {
            $warnings->push('Co ' . $missingCount . ' ban ghi thieu check-in/check-out.');
        }

        if ($pendingRecords->count() > 0) {
            $warnings->push('Co ' . $pendingRecords->count() . ' ban ghi cho duyet.');
        }

        $rejectedCount = $records->where('approval_status', 'rejected')->count();
        if ($rejectedCount > 0) {
            $warnings->push('Co ' . $rejectedCount . ' ban ghi bi tu choi.');
        }

        if ($approvedRecords->isEmpty() && $records->isNotEmpty()) {
            $warnings->push('Chua co cong duyet hop le de tinh luong.');
        }

        return $warnings->unique()->values();
    }

    private function countExpectedWorkDays(
        EmployeeProfile $profile,
        int $month,
        int $year,
        ?array $holidayMap = null,
        ?Collection $shiftAssignments = null
    ): int
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $end = $this->resolveExpectedWorkdayWindowEnd($month, $year);

        if ($end->lessThan($start)) {
            return 0;
        }

        $holidayMap ??= $this->holidayMap($month, $year);
        $shiftAssignments ??= $this->shiftAssignmentsForPeriod($profile, $month, $year);
        $count = 0;

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if (!$this->isExpectedWorkingDate($profile, $date, $shiftAssignments)) {
                continue;
            }

            $holiday = $holidayMap[$date->toDateString()] ?? null;
            if ($holiday && !($holiday['is_paid_leave'] ?? false)) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    private function resolveExpectedWorkdayWindowEnd(int $month, int $year): Carbon
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $endOfMonth = $start->copy()->endOfMonth();
        $today = Carbon::now(self::TIMEZONE)->startOfDay();

        if ($start->isSameMonth($today) && $start->year === $today->year) {
            return $today->copy();
        }

        if ($start->greaterThan($today)) {
            return $start->copy()->subDay();
        }

        return $endOfMonth;
    }

    private function shiftAssignmentsForPeriod(EmployeeProfile $profile, int $month, int $year): Collection
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $end = $this->resolveExpectedWorkdayWindowEnd($month, $year);

        if ($end->lessThan($start)) {
            return collect();
        }

        return EmployeeWorkShiftAssignment::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($profile) {
                $query->where('employee_profile_id', $profile->id);

                if ($profile->department_id) {
                    $query->orWhere(function (Builder $departmentQuery) use ($profile) {
                        $departmentQuery
                            ->whereNull('employee_profile_id')
                            ->where('department_id', $profile->department_id);
                    });
                }

                $query->orWhere(function (Builder $companyQuery) {
                    $companyQuery
                        ->whereNull('employee_profile_id')
                        ->whereNull('department_id');
                });
            })
            ->whereDate('effective_from', '<=', $end->toDateString())
            ->where(function (Builder $query) use ($start) {
                $query
                    ->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $start->toDateString());
            })
            ->orderByRaw('case when employee_profile_id is not null then 2 when department_id is not null then 1 else 0 end desc')
            ->orderByDesc('effective_from')
            ->get();
    }

    private function isExpectedWorkingDate(EmployeeProfile $profile, Carbon $date, ?Collection $shiftAssignments = null): bool
    {
        $shiftAssignments ??= collect();
        $workDate = $date->copy()->startOfDay();
        $weekday = (int) $workDate->dayOfWeekIso;

        $matchedAssignment = $shiftAssignments->first(function (EmployeeWorkShiftAssignment $assignment) use ($workDate, $weekday) {
            $effectiveFrom = $assignment->effective_from?->copy()->startOfDay();
            $effectiveTo = $assignment->effective_to?->copy()->startOfDay();

            if (!$effectiveFrom || $effectiveFrom->greaterThan($workDate)) {
                return false;
            }

            if ($effectiveTo && $effectiveTo->lessThan($workDate)) {
                return false;
            }

            $weekdays = collect($assignment->weekdays ?? [])
                ->map(fn ($value) => (int) $value)
                ->filter(fn (int $value) => $value >= 1 && $value <= 7)
                ->values();

            return $weekdays->isEmpty() || $weekdays->contains($weekday);
        });

        if ($shiftAssignments->isNotEmpty()) {
            return (bool) $matchedAssignment;
        }

        return !$workDate->isWeekend();
    }

    private function holidayMap(int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return Holiday::query()
            ->whereBetween('holiday_date', [$start->toDateString(), $end->toDateString()])
            ->get(['holiday_date', 'holiday_name', 'holiday_type', 'is_paid_leave'])
            ->mapWithKeys(function (Holiday $holiday) {
                $date = Carbon::parse($holiday->holiday_date, self::TIMEZONE)->toDateString();

                return [$date => [
                    'holiday_name' => $holiday->holiday_name,
                    'holiday_type' => $holiday->holiday_type,
                    'is_paid_leave' => (bool) $holiday->is_paid_leave,
                ]];
            })
            ->all();
    }

    private function buildPaidHolidayRows(
        EmployeeProfile $profile,
        $records,
        array $holidayMap,
        float $dailyRate,
        ?Collection $shiftAssignments = null
    )
    {
        $shiftAssignments ??= collect();
        $recordDates = $records
            ->map(fn (AttendanceRecord $record) => optional($record->work_date)->format('Y-m-d'))
            ->filter()
            ->flip();

        return collect($holidayMap)
            ->filter(fn (array $holiday, string $date) => (bool) ($holiday['is_paid_leave'] ?? false) && !$recordDates->has($date))
            ->reject(fn (array $holiday, string $date) => !$this->isExpectedWorkingDate($profile, Carbon::parse($date, self::TIMEZONE), $shiftAssignments))
            ->map(fn (array $holiday, string $date) => [
                'id' => 'holiday-' . $date,
                'work_date' => $date,
                'shift_name' => $holiday['holiday_name'] ?? 'Ngay le',
                'shift_time_range' => 'Nghi le co luong',
                'shift_standard_minutes' => self::DEFAULT_FULL_DAY_MINUTES,
                'shift_half_day_minutes' => self::DEFAULT_HALF_DAY_MINUTES,
                'worked_minutes' => 0,
                'work_unit' => 1.0,
                'payable_amount' => round($dailyRate, 2),
                'overtime_minutes' => 0,
                'overtime_multiplier' => self::OVERTIME_HOLIDAY_MULTIPLIER,
                'overtime_type_label' => 'Ngay le',
                'overtime_amount' => 0.0,
                'attendance_status' => 'on_time',
                'day_status' => 'holiday_paid',
                'approval_status' => 'approved',
                'approval_note' => 'Ngay le co luong',
            ])
            ->values();
    }

    private function resolvePayableWorkUnit(AttendanceRecord $record): float
    {
        $dayStatus = $this->resolvePayableDayStatus($record);

        if ($dayStatus === 'leave') {
            $workedMinutes = $this->resolveWorkedMinutes($record);

            if ($workedMinutes > 0) {
                if ($workedMinutes >= max(1, $this->resolveShiftStandardMinutes($record))) {
                    return 1.0;
                }

                if ($workedMinutes >= max(1, $this->resolveShiftHalfDayMinutes($record))) {
                    return 0.5;
                }
            }

            return 1.0;
        }

        if ($dayStatus === 'business_trip') {
            return 1.0;
        }

        if (in_array($dayStatus, ['unpaid_leave', 'absent'], true)) {
            return 0.0;
        }

        if ($record->missing_check_in || $record->missing_check_out) {
            return 0.0;
        }

        $workedMinutes = $this->resolveWorkedMinutes($record);
        $fullDayMinutes = $this->resolveShiftStandardMinutes($record);
        $halfDayMinutes = $this->resolveShiftHalfDayMinutes($record);

        if (
            $record->check_in_at
            && $record->check_out_at
            && $this->resolveLateMinutes($record) === 0
            && $this->resolveEarlyLeaveMinutes($record) === 0
            && $this->resolvePayableAttendanceStatus($record) !== 'absent'
        ) {
            return 1.0;
        }

        if ($workedMinutes >= max(1, $fullDayMinutes)) {
            return 1.0;
        }

        if ($workedMinutes >= max(1, $halfDayMinutes)) {
            return 0.5;
        }

        return 0.0;
    }

    private function resolvePayableAttendanceStatus(AttendanceRecord $record): string
    {
        if ((string) ($record->attendance_status ?? '') === 'absent' && !$record->check_in_at) {
            return 'absent';
        }

        return $this->resolveLateMinutes($record) > 0 ? 'late' : 'on_time';
    }

    private function resolvePayableDayStatus(AttendanceRecord $record): string
    {
        $dayStatus = (string) ($record->day_status ?? '');

        if (in_array($dayStatus, ['leave', 'unpaid_leave', 'business_trip', 'absent'], true)) {
            return $dayStatus;
        }

        if ($record->missing_check_in) {
            return 'missing_check_in';
        }

        if ($record->missing_check_out || ($record->check_in_at && !$record->check_out_at)) {
            return 'missing_check_out';
        }

        if ($this->resolveEarlyLeaveMinutes($record) > 0) {
            return 'early_leave';
        }

        if ($this->resolveLateMinutes($record) > 0) {
            return 'late';
        }

        return 'present';
    }

    private function resolveApprovalStatus(AttendanceRecord $record): string
    {
        if (($record->approval_status ?? null) === 'rejected') {
            return 'rejected';
        }

        return $this->isEffectivelyApproved($record) ? 'approved' : 'pending';
    }

    private function isEffectivelyApproved(AttendanceRecord $record): bool
    {
        if (($record->approval_status ?? null) === 'approved' || (bool) $record->is_confirmed) {
            return true;
        }

        return AttendanceAdjustment::query()
            ->where('attendance_record_id', $record->id)
            ->where('status', 'approved')
            ->exists();
    }

    private function resolveOvertimeMultiplier(AttendanceRecord $record, array $holidayMap): float
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if (!$workDate) {
            return self::OVERTIME_WEEKDAY_MULTIPLIER;
        }

        if (isset($holidayMap[$workDate])) {
            return self::OVERTIME_HOLIDAY_MULTIPLIER;
        }

        $date = Carbon::parse($workDate, self::TIMEZONE);

        return $date->isWeekend()
            ? self::OVERTIME_WEEKEND_MULTIPLIER
            : self::OVERTIME_WEEKDAY_MULTIPLIER;
    }

    private function resolveOvertimeTypeLabel(AttendanceRecord $record, array $holidayMap): string
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if ($workDate && isset($holidayMap[$workDate])) {
            return 'Ngay le';
        }

        if ($workDate && Carbon::parse($workDate, self::TIMEZONE)->isWeekend()) {
            return 'Ngay nghi tuan';
        }

        return 'Ngay thuong';
    }

    private function resolveOvertimeAmount(AttendanceRecord $record, float $hourlyRate, array $holidayMap): float
    {
        $overtimeMinutes = $this->resolveApprovedOvertimeMinutes($record);
        if ($overtimeMinutes === 0) {
            return 0.0;
        }

        $configuredHourlyRate = $this->resolveOvertimeHourlyRate($record);

        if ($configuredHourlyRate > 0) {
            return $configuredHourlyRate * ($overtimeMinutes / 60);
        }

        return $hourlyRate * ($overtimeMinutes / 60) * $this->resolveOvertimeMultiplier($record, $holidayMap);
    }

    private function resolveOvertimeHourlyRate(AttendanceRecord $record): float
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return max(0.0, (float) ($snapshot['overtime_hourly_rate'] ?? 0));
    }

    private function resolveApprovedOvertimeMinutes(AttendanceRecord $record): int
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if (!$workDate) {
            return 0;
        }

        $snapshot = $this->resolveShiftSnapshot($record);

        if (array_key_exists('allows_overtime', $snapshot) && !((bool) $snapshot['allows_overtime'])) {
            return 0;
        }

        $overtimeWindow = $this->resolveOvertimeWindow($record, $snapshot);
        if (!$overtimeWindow) {
            return $this->sumApprovedOvertimeMinutesForRecordDate($record);
        }

        [$overtimeStart, $overtimeEnd] = $overtimeWindow;

        return (int) OvertimeRequest::query()
            ->where('employee_profile_id', (int) $record->employee_profile_id)
            ->whereDate('work_date', $workDate)
            ->where('status', 'approved')
            ->get(['approved_minutes', 'start_at', 'end_at'])
            ->sum(function (OvertimeRequest $request) use ($overtimeStart, $overtimeEnd, $snapshot) {
                $approvedMinutes = max(0, (int) ($request->approved_minutes ?? 0));
                if ($approvedMinutes === 0 || !$request->start_at || !$request->end_at) {
                    return 0;
                }

                $requestStart = Carbon::parse($request->start_at, self::TIMEZONE);
                $requestEnd = Carbon::parse($request->end_at, self::TIMEZONE);
                $catalogMinutes = $this->calculatePaidOvertimeMinutes($requestStart, $requestEnd, $overtimeStart, $overtimeEnd, $snapshot);

                return min($approvedMinutes, $catalogMinutes);
            });
    }

    private function resolveWorkedMinutes(AttendanceRecord $record): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        if (!$record->check_in_at || !$record->check_out_at) {
            return max(0, (int) ($record->worked_minutes ?? 0));
        }

        $minutes = max(0, (int) $record->check_in_at->diffInMinutes($record->check_out_at, false));
        $breakMinutes = $this->resolveBreakOverlapMinutes($record, $snapshot);
        return max(0, $minutes - $breakMinutes);
    }

    private function resolveLateMinutes(AttendanceRecord $record): int
    {
        if (!$record->check_in_at) {
            return max(0, (int) ($record->late_minutes ?? 0));
        }

        $checkInAt = Carbon::parse($record->check_in_at, self::TIMEZONE);
        [$expectedStart] = $this->shiftBoundaries($checkInAt, $this->resolveShiftSnapshot($record));
        $allowedMinutes = $this->resolveGraceMinutes($record, 'late_grace_minutes', 10);
        $diffMinutes = (int) $expectedStart->diffInMinutes($checkInAt, false);

        return max(0, $diffMinutes - $allowedMinutes);
    }

    private function resolveEarlyLeaveMinutes(AttendanceRecord $record): int
    {
        if (!$record->check_out_at) {
            return max(0, (int) ($record->early_leave_minutes ?? 0));
        }

        $checkOutAt = Carbon::parse($record->check_out_at, self::TIMEZONE);
        [, $expectedEnd] = $this->shiftBoundaries($checkOutAt, $this->resolveShiftSnapshot($record));
        $allowedMinutes = $this->resolveGraceMinutes($record, 'early_leave_grace_minutes', 5);
        $diffMinutes = (int) $expectedEnd->diffInMinutes($checkOutAt, false);

        return $diffMinutes < 0 ? max(0, abs($diffMinutes) - $allowedMinutes) : 0;
    }

    private function resolveGraceMinutes(AttendanceRecord $record, string $specificKey, int $defaultMinutes): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);
        $minutes = (int) (($snapshot[$specificKey] ?? null) ?? ($snapshot['grace_minutes'] ?? $defaultMinutes));

        return $minutes > 0 ? $minutes : $defaultMinutes;
    }

    private function shiftBoundaries(Carbon $dateTime, array $snapshot): array
    {
        $startTime = (string) ($snapshot['start_time'] ?? '08:00:00');
        $endTime = (string) ($snapshot['end_time'] ?? '17:30:00');
        $baseDate = $this->resolveShiftBoundaryBaseDate($dateTime, $snapshot);
        $start = $this->resolveTimePoint($baseDate, $startTime);
        $end = $this->resolveTimePoint(
            $baseDate,
            $endTime,
            (bool) ($snapshot['is_overnight'] ?? false) && $this->minutesOfDay($endTime) <= $this->minutesOfDay($startTime)
        );

        return [$start, $end];
    }

    private function resolveShiftBoundaryBaseDate(Carbon $dateTime, array $snapshot): Carbon
    {
        $baseDate = $dateTime->copy()->startOfDay();
        $startTime = (string) ($snapshot['start_time'] ?? '08:00:00');
        $endTime = (string) ($snapshot['end_time'] ?? '17:30:00');
        $isOvernight = (bool) ($snapshot['is_overnight'] ?? false)
            && $this->minutesOfDay($endTime) <= $this->minutesOfDay($startTime);

        if ($isOvernight) {
            $currentMinutes = ((int) $dateTime->copy()->timezone(self::TIMEZONE)->format('H') * 60)
                + (int) $dateTime->copy()->timezone(self::TIMEZONE)->format('i');

            if ($currentMinutes <= $this->minutesOfDay($endTime)) {
                $baseDate->subDay();
            }
        }

        return $baseDate;
    }

    private function resolveBreakOverlapMinutes(AttendanceRecord $record, array $snapshot): int
    {
        $breakStart = $snapshot['break_start_time'] ?? null;
        $breakEnd = $snapshot['break_end_time'] ?? null;

        if (!$breakStart || !$breakEnd || !$record->check_in_at || !$record->check_out_at) {
            return 0;
        }

        $checkInAt = Carbon::parse($record->check_in_at, self::TIMEZONE);
        $checkOutAt = Carbon::parse($record->check_out_at, self::TIMEZONE);
        $breakStartAt = $this->resolveTimePoint($checkInAt, (string) $breakStart);
        $breakEndAt = $this->resolveTimePoint(
            $checkInAt,
            (string) $breakEnd,
            (bool) ($snapshot['is_overnight'] ?? false) && $this->minutesOfDay((string) $breakEnd) <= $this->minutesOfDay((string) $breakStart)
        );

        $start = $checkInAt->greaterThan($breakStartAt) ? $checkInAt : $breakStartAt;
        $end = $checkOutAt->lessThan($breakEndAt) ? $checkOutAt : $breakEndAt;

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return max(0, (int) $start->diffInMinutes($end, false));
    }

    private function resolveHandoverBreakOverlapMinutes(AttendanceRecord $record, array $snapshot): int
    {
        $handoverMinutes = max(0, (int) ($snapshot['handover_break_minutes'] ?? 0));
        $endTime = $snapshot['end_time'] ?? null;

        if ($handoverMinutes === 0 || !$endTime || !$record->check_in_at || !$record->check_out_at) {
            return 0;
        }

        $checkInAt = Carbon::parse($record->check_in_at, self::TIMEZONE);
        $checkOutAt = Carbon::parse($record->check_out_at, self::TIMEZONE);
        $shiftEndAt = $this->resolveTimePoint(
            $checkInAt,
            (string) $endTime,
            (bool) ($snapshot['is_overnight'] ?? false)
                && $this->minutesOfDay((string) $endTime) <= $this->minutesOfDay((string) ($snapshot['start_time'] ?? $endTime))
        );
        $handoverStartAt = $shiftEndAt->copy()->subMinutes($handoverMinutes);
        $start = $checkInAt->greaterThan($handoverStartAt) ? $checkInAt : $handoverStartAt;
        $end = $checkOutAt->lessThan($shiftEndAt) ? $checkOutAt : $shiftEndAt;

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return max(0, (int) $start->diffInMinutes($end, false));
    }

    private function sumApprovedOvertimeMinutesForRecordDate(AttendanceRecord $record): int
    {
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if (!$workDate) {
            return 0;
        }

        return (int) OvertimeRequest::query()
            ->where('employee_profile_id', (int) $record->employee_profile_id)
            ->whereDate('work_date', $workDate)
            ->where('status', 'approved')
            ->sum('approved_minutes');
    }

    private function resolveShiftStandardMinutes(AttendanceRecord $record): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return max(1, (int) ($snapshot['standard_minutes'] ?? self::DEFAULT_FULL_DAY_MINUTES));
    }

    private function resolveShiftHalfDayMinutes(AttendanceRecord $record): int
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return max(1, (int) ($snapshot['half_day_minutes'] ?? self::DEFAULT_HALF_DAY_MINUTES));
    }

    private function resolveShiftName(AttendanceRecord $record): string
    {
        $snapshot = $this->resolveShiftSnapshot($record);

        return (string) ($snapshot['shift_name'] ?? $record->workShift?->shift_name ?? 'Ca mac dinh');
    }

    private function resolveShiftTimeRange(AttendanceRecord $record): string
    {
        $snapshot = $this->resolveShiftSnapshot($record);
        $startTime = $snapshot['start_time'] ?? $record->workShift?->start_time;
        $endTime = $snapshot['end_time'] ?? $record->workShift?->end_time;
        $isOvernight = (bool) ($snapshot['is_overnight'] ?? $record->workShift?->is_overnight ?? false);

        if (!$startTime || !$endTime) {
            return '-';
        }

        return substr((string) $startTime, 0, 5) . ' - ' . substr((string) $endTime, 0, 5) . ($isOvernight ? ' (+1)' : '');
    }

    private function resolveShiftSnapshot(AttendanceRecord $record): array
    {
        $snapshot = is_array($record->shift_snapshot) ? $record->shift_snapshot : [];
        $liveConfig = $record->workShift ? [
            'shift_name' => $record->workShift->shift_name,
            'start_time' => $record->workShift->start_time,
            'end_time' => $record->workShift->end_time,
            'break_start_time' => $record->workShift->break_start_time,
            'break_end_time' => $record->workShift->break_end_time,
            'standard_minutes' => $record->workShift->standard_minutes,
            'half_day_minutes' => $record->workShift->half_day_minutes,
            'handover_break_minutes' => $record->workShift->handover_break_minutes,
            'grace_minutes' => $record->workShift->grace_minutes,
            'late_grace_minutes' => $record->workShift->late_grace_minutes,
            'early_leave_grace_minutes' => $record->workShift->early_leave_grace_minutes,
            'overtime_start_time' => $record->workShift->overtimeRule?->start_time,
            'overtime_end_time' => $record->workShift->overtimeRule?->end_time,
            'overtime_hourly_rate' => $record->workShift->overtimeRule?->hourly_rate,
            'allows_overtime' => $record->workShift->allows_overtime,
            'is_overnight' => $record->workShift->is_overnight,
        ] : [];

        return array_merge($liveConfig, $snapshot);
    }

    private function resolveOvertimeWindow(AttendanceRecord $record, array $snapshot): ?array
    {
        $startTime = $snapshot['overtime_start_time'] ?? null;
        $endTime = $snapshot['overtime_end_time'] ?? null;
        $workDate = optional($record->work_date)?->format('Y-m-d');

        if (!$workDate || !$startTime || !$endTime) {
            return null;
        }

        $baseDate = Carbon::parse($workDate, self::TIMEZONE);
        $startAt = $this->resolveTimePoint($baseDate, (string) $startTime);
        $endAt = $this->resolveTimePoint(
            $baseDate,
            (string) $endTime,
            $this->minutesOfDay((string) $endTime) <= $this->minutesOfDay((string) $startTime)
        );

        return $endAt->greaterThan($startAt) ? [$startAt, $endAt] : null;
    }

    private function calculateOverlapMinutes(Carbon $startA, Carbon $endA, Carbon $startB, Carbon $endB): int
    {
        $start = $startA->greaterThan($startB) ? $startA : $startB;
        $end = $endA->lessThan($endB) ? $endA : $endB;

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return max(0, (int) $start->diffInMinutes($end, false));
    }

    private function calculatePaidOvertimeMinutes(Carbon $rangeStart, Carbon $rangeEnd, Carbon $windowStart, Carbon $windowEnd, array $snapshot): int
    {
        return $this->calculateOverlapMinutes($rangeStart, $rangeEnd, $windowStart, $windowEnd);
    }

    private function resolveTimePoint(Carbon $baseDate, string $time, bool $nextDay = false): Carbon
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));
        $point = $baseDate->copy()->startOfDay()->setTime($hour, $minute);

        return $nextDay ? $point->addDay() : $point;
    }

    private function minutesOfDay(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hour * 60) + $minute;
    }
}
