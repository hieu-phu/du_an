<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Project;
use App\Models\ProjectImplementationDetail;
use App\Models\User;
use App\Support\AccessMatrix;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $payload = $this->buildReportPayload($request->user(), $request->all());

        return Inertia::render('Reports/Index', $payload);
    }

    public function exportExcel(Request $request)
    {
        $payload = $this->buildReportPayload($request->user(), $request->all());
        $filters = $payload['filters'];
        $filename = sprintf('general-report-%04d-%02d.xls', $filters['year'], $filters['month']);

        $content = view('exports.general-report-excel', [
            'filters' => $filters,
            'employeeByDepartment' => $payload['employeeByDepartment'],
            'projectByStatus' => $payload['projectByStatus'],
            'projectProgress' => $payload['projectProgress'],
            'attendanceMonthly' => $payload['attendanceMonthly'],
            'scopeLabel' => $payload['scopeLabel'],
        ])->render();

        return response("\xEF\xBB\xBF" . $content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $payload = $this->buildReportPayload($request->user(), $request->all());
        $filters = $payload['filters'];
        $filename = sprintf('general-report-%04d-%02d.pdf', $filters['year'], $filters['month']);

        $html = view('exports.general-report-pdf', [
            'filters' => $filters,
            'employeeByDepartment' => $payload['employeeByDepartment'],
            'projectByStatus' => $payload['projectByStatus'],
            'projectProgress' => $payload['projectProgress'],
            'attendanceMonthly' => $payload['attendanceMonthly'],
            'scopeLabel' => $payload['scopeLabel'],
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function buildReportPayload(User $user, array $input): array
    {
        $month = max(1, min(12, (int) ($input['month'] ?? now()->month)));
        $year = max(2020, min(2100, (int) ($input['year'] ?? now()->year)));
        $departmentId = !empty($input['department_id']) ? (int) $input['department_id'] : null;
        $projectStatus = !empty($input['project_status']) ? (string) $input['project_status'] : null;

        $profileId = $user->employeeProfile?->id;
        $isEmployee = $user->hasRole(AccessMatrix::ROLE_EMPLOYEE);
        $isAdmin = $user->hasRole(AccessMatrix::ROLE_ADMIN);
        $isHr = $user->hasRole(AccessMatrix::ROLE_HR) && !$isAdmin;

        $employeeByDepartment = $this->buildEmployeeByDepartment($user, $departmentId);
        $projectByStatus = $isHr ? [] : $this->buildProjectByStatus($user, $profileId, $projectStatus);
        $projectProgress = $isHr ? [] : $this->buildProjectProgress($user, $profileId, $projectStatus);
        $attendanceMonthly = $this->buildAttendanceMonthly($user, $profileId, $month, $year, $departmentId);

        return [
            'filters' => [
                'month' => $month,
                'year' => $year,
                'department_id' => $departmentId,
                'project_status' => $projectStatus,
            ],
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'statusOptions' => [
                ['value' => 'planning', 'label' => 'Ke hoach'],
                ['value' => 'in_progress', 'label' => 'Dang trien khai'],
                ['value' => 'on_hold', 'label' => 'Tam dung'],
                ['value' => 'completed', 'label' => 'Hoan thanh'],
            ],
            'employeeByDepartment' => $employeeByDepartment,
            'projectByStatus' => $projectByStatus,
            'projectProgress' => $projectProgress,
            'attendanceMonthly' => $attendanceMonthly,
            'scopeLabel' => $isEmployee ? 'Du lieu ca nhan' : 'Du lieu toan bo',
            'canViewAll' => !$isEmployee,
            'canViewProjectReports' => !$isHr,
        ];
    }

    private function buildEmployeeByDepartment(User $user, ?int $departmentId): array
    {
        if ($user->hasRole(AccessMatrix::ROLE_EMPLOYEE)) {
            $profile = $user->employeeProfile;
            if (!$profile) {
                return [];
            }

            return [[
                'department_name' => $profile->department?->name ?? '-',
                'employee_count' => 1,
            ]];
        }

        return EmployeeProfile::query()
            ->with('department:id,name')
            ->when($departmentId, fn (Builder $q) => $q->where('department_id', $departmentId))
            ->get()
            ->groupBy('department_id')
            ->map(function ($profiles) {
                $first = $profiles->first();
                return [
                    'department_name' => $first?->department?->name ?? 'Chua gan phong ban',
                    'employee_count' => $profiles->count(),
                ];
            })
            ->values()
            ->all();
    }

    private function buildProjectByStatus(User $user, ?int $profileId, ?string $projectStatus): array
    {
        $query = $this->projectBaseQueryForUser($user, $profileId)
            ->when($projectStatus, fn (Builder $q) => $q->where('status', $projectStatus));

        $counts = $query
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusMap = [
            'planning' => 'Ke hoach',
            'in_progress' => 'Dang trien khai',
            'on_hold' => 'Tam dung',
            'completed' => 'Hoan thanh',
        ];

        return collect($statusMap)->map(function (string $label, string $status) use ($counts) {
            return [
                'status' => $status,
                'status_label' => $label,
                'count' => (int) ($counts[$status] ?? 0),
            ];
        })->values()->all();
    }

    private function buildProjectProgress(User $user, ?int $profileId, ?string $projectStatus): array
    {
        $projects = $this->projectBaseQueryForUser($user, $profileId)
            ->when($projectStatus, fn (Builder $q) => $q->where('status', $projectStatus))
            ->latest('start_date')
            ->limit(50)
            ->get(['id', 'name', 'status', 'start_date']);

        if ($projects->isEmpty()) {
            return [];
        }

        $projectIds = $projects->pluck('id')->all();

        $detailStats = ProjectImplementationDetail::query()
            ->select(
                'project_id',
                DB::raw("SUM(CASE WHEN detail_status <> 'cancelled' THEN COALESCE(duration_days, 0) ELSE 0 END) as total_duration"),
                DB::raw("SUM(CASE WHEN detail_status = 'completed' THEN COALESCE(duration_days, 0) ELSE 0 END) as completed_duration"),
                DB::raw("SUM(CASE WHEN detail_status <> 'cancelled' THEN 1 ELSE 0 END) as total_tasks"),
                DB::raw("SUM(CASE WHEN detail_status = 'completed' THEN 1 ELSE 0 END) as completed_tasks")
            )
            ->whereIn('project_id', $projectIds)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id');

        return $projects->map(function (Project $project) use ($detailStats) {
            $stat = $detailStats->get($project->id);
            $totalDuration = (int) ($stat?->total_duration ?? 0);
            $completedDuration = (int) ($stat?->completed_duration ?? 0);
            $progress = $totalDuration > 0 ? (int) round(($completedDuration / $totalDuration) * 100) : 0;

            return [
                'project_name' => $project->name,
                'status' => (string) $project->status,
                'status_label' => $this->projectStatusLabel((string) $project->status),
                'start_date' => optional($project->start_date)->toDateString(),
                'progress_percent' => max(0, min(100, $progress)),
                'completed_tasks' => (int) ($stat?->completed_tasks ?? 0),
                'total_tasks' => (int) ($stat?->total_tasks ?? 0),
            ];
        })->values()->all();
    }

    private function buildAttendanceMonthly(User $user, ?int $profileId, int $month, int $year, ?int $departmentId): array
    {
        $query = AttendanceRecord::query()
            ->with('employeeProfile.department:id,name')
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year);

        if ($user->hasRole(AccessMatrix::ROLE_EMPLOYEE) && $profileId) {
            $query->where('employee_profile_id', $profileId);
        } elseif ($departmentId) {
            $query->whereHas('employeeProfile', fn (Builder $q) => $q->where('department_id', $departmentId));
        }

        $records = $query->get();

        return [
            'total_records' => $records->count(),
            'on_time_records' => $records->whereIn('attendance_status', ['on_time', 'present'])->count(),
            'late_records' => $records->filter(fn (AttendanceRecord $r) => $r->attendance_status === 'late' || (int) ($r->late_minutes ?? 0) > 0)->count(),
            'absent_records' => $records->where('attendance_status', 'absent')->count(),
            'early_leave_records' => $records->filter(fn (AttendanceRecord $r) => ($r->day_status === 'early_leave') || (int) ($r->early_leave_minutes ?? 0) > 0)->count(),
            'worked_minutes' => (int) $records->sum(fn (AttendanceRecord $r) => (int) ($r->worked_minutes ?? 0)),
        ];
    }

    private function projectBaseQueryForUser(User $user, ?int $profileId): Builder
    {
        $query = Project::query();

        if ($user->hasRole(AccessMatrix::ROLE_EMPLOYEE) && $profileId) {
            $query->whereHas('members', function (Builder $builder) use ($profileId) {
                $builder->where('employee_profile_id', $profileId)->where('is_active', true);
            });
        }

        return $query;
    }

    private function projectStatusLabel(string $status): string
    {
        return match ($status) {
            'planning' => 'Ke hoach',
            'in_progress' => 'Dang trien khai',
            'on_hold' => 'Tam dung',
            'completed' => 'Hoan thanh',
            default => '-',
        };
    }
}
