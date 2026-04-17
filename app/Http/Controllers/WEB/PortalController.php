<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\Position;
use App\Models\Project;
use App\Models\ProjectImplementationDetail;
use App\Models\ProjectMember;
use App\Models\User;
use App\Support\AccessMatrix;
use App\Repositories\AttendanceRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Exceptions\DriverException;

class PortalController extends Controller
{
    public function __construct(
        protected AttendanceRepository $attendanceRepository
    ) {}

    public function dashboard(Request $request): Response
    {
        $user = $request->user();
        $profileId = $user?->employeeProfile?->id;
        $todayRecord = null;

        if ($profileId) {
            $todayRecord = AttendanceRecord::query()
                ->where('employee_profile_id', $profileId)
                ->whereDate('work_date', now('Asia/Ho_Chi_Minh')->toDateString())
                ->first();
        }

        $stats = [
            [
                'title' => 'Nhan su',
                'value' => (string) User::query()->where('is_employee', 1)->count(),
            ],
            [
                'title' => 'Phong ban',
                'value' => (string) Department::query()->count(),
            ],
            [
                'title' => 'Chuc vu',
                'value' => (string) Position::query()->count(),
            ],
            [
                'title' => 'Du an',
                'value' => (string) Project::query()->count(),
            ],
        ];

        if ($user && !$this->hasGlobalProjectAccess($user) && $profileId) {
            $stats = [
                [
                    'title' => 'Du an cua toi',
                    'value' => (string) ProjectMember::query()
                        ->where('employee_profile_id', $profileId)
                        ->where('is_active', true)
                        ->count(),
                ],
                [
                    'title' => 'Cong viec du an',
                    'value' => (string) ProjectImplementationDetail::query()
                        ->where('assigned_to', $profileId)
                        ->count(),
                ],
                [
                    'title' => 'Cong thang nay',
                    'value' => (string) AttendanceRecord::query()
                        ->where('employee_profile_id', $profileId)
                        ->whereMonth('work_date', now('Asia/Ho_Chi_Minh')->month)
                        ->whereYear('work_date', now('Asia/Ho_Chi_Minh')->year)
                        ->count(),
                ],
                [
                    'title' => 'Trạng thái hôm nay',
                    'value' => $this->attendanceStatusLabel($todayRecord?->attendance_status),
                ],
            ];
        }

        $warnings = [];

        if ($profileId) {
            $missedPunches = $this->attendanceRepository->getMissedPunchCount($profileId);
            if ($missedPunches > 0) {
                $warnings[] = [
                    'type' => 'danger',
                    'title' => 'Quên chấm công',
                    'message' => "Bạn có $missedPunches lần quên chấm công trong 30 ngày qua.",
                    'cta_label' => 'Xem chi tiết',
                    'cta_url' => route('attendance.mine'),
                ];
            }

            $lateCount = $this->attendanceRepository->getFrequentLateCount($profileId);
            if ($lateCount >= 3) {
                $warnings[] = [
                    'type' => 'warning',
                    'title' => 'Đi muộn nhiều lần',
                    'message' => "Bạn đã đi muộn $lateCount lần trong tháng này. Hãy chú ý giờ giấc!",
                    'cta_label' => 'Xem lịch sử',
                    'cta_url' => route('attendance.mine'),
                ];
            }

            $unconfirmed = $this->attendanceRepository->getUnconfirmedRecordsCount($profileId);
            if ($unconfirmed > 0 && now()->day >= 25) {
                $warnings[] = [
                    'type' => 'info',
                    'title' => 'Chưa duyệt công cuối tháng',
                    'message' => "Bạn còn $unconfirmed bản ghi công chưa xác nhận trong tháng này.",
                    'cta_label' => 'Xác nhận ngay',
                    'cta_url' => route('attendance.mine'),
                ];
            }
        }

        // Dành cho Quản lý / Admin
        if (AccessMatrix::canManageAllAttendance($user)) {
            $pendingApprovals = $this->attendanceRepository->getPendingApprovalsCount();
            if ($pendingApprovals > 0) {
                $warnings[] = [
                    'type' => 'primary',
                    'title' => 'Yêu cầu chờ duyệt',
                    'message' => "Có $pendingApprovals yêu cầu chấm công đang chờ bạn phê duyệt.",
                    'cta_label' => 'Tới trang duyệt',
                    'cta_url' => route('attendance.approvals'),
                ];
            }
        }

        $dashboardSummary = $this->buildDashboardSummary($user, $profileId);

        return Inertia::render('DashBoard', [
            'stats' => $stats,
            'warnings' => $warnings,
            'dashboardSummary' => $dashboardSummary,
            'todayAttendance' => $todayRecord ? [
                'work_date' => $todayRecord->work_date,
                'check_in_at' => $todayRecord->check_in_at,
                'check_out_at' => $todayRecord->check_out_at,
                'status' => $todayRecord->attendance_status,
                'status_label' => $this->attendanceStatusLabel($todayRecord->attendance_status),
            ] : null,
        ]);
    }

    private function attendanceStatusLabel(?string $status): string
    {
        return match ($status) {
            'on_time', 'present' => 'Đúng giờ',
            'late', 'half_day' => 'Trễ',
            'absent', 'leave', 'pending' => 'Vắng',
            default => 'Chưa chấm công',
        };
    }

    private function buildDashboardSummary(User $user, ?int $profileId): array
    {
        $projectBaseQuery = $this->projectBaseQueryForUser($user, $profileId);

        return [
            'total_employees' => User::query()->where('is_employee', 1)->count(),
            'total_projects' => (clone $projectBaseQuery)->count(),
            'project_status_counts' => $this->buildProjectStatusCounts($projectBaseQuery),
            'active_project_progress' => $this->buildActiveProjectProgress($projectBaseQuery),
            'attendance_month_report' => $this->buildAttendanceMonthReport($user, $profileId),
        ];
    }

    private function projectBaseQueryForUser(User $user, ?int $profileId): Builder
    {
        $query = Project::query();

        if (!$this->hasGlobalProjectAccess($user) && $profileId) {
            $query->whereHas('members', function (Builder $builder) use ($profileId) {
                $builder
                    ->where('employee_profile_id', $profileId)
                    ->where('is_active', true);
            });
        }

        return $query;
    }

    private function buildProjectStatusCounts(Builder $projectBaseQuery): array
    {
        $statusMap = [
            'planning' => 'Ke hoach',
            'in_progress' => 'Dang trien khai',
            'on_hold' => 'Tam dung',
            'completed' => 'Hoan thanh',
        ];

        $counts = (clone $projectBaseQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect($statusMap)->map(function (string $label, string $status) use ($counts) {
            return [
                'status' => $status,
                'label' => $label,
                'count' => (int) ($counts[$status] ?? 0),
            ];
        })->values()->all();
    }

    private function buildActiveProjectProgress(Builder $projectBaseQuery): array
    {
        $projects = (clone $projectBaseQuery)
            ->whereIn('status', ['planning', 'in_progress', 'on_hold'])
            ->orderByDesc('start_date')
            ->limit(10)
            ->get(['id', 'name', 'status', 'start_date']);

        if ($projects->isEmpty()) {
            return [];
        }

        $projectIds = $projects->pluck('id')->all();

        $detailSummaries = ProjectImplementationDetail::query()
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

        return $projects->map(function (Project $project) use ($detailSummaries) {
            $summary = $detailSummaries->get($project->id);
            $totalDuration = max(0, (int) ($summary?->total_duration ?? 0));
            $completedDuration = max(0, (int) ($summary?->completed_duration ?? 0));
            $progressPercent = $totalDuration > 0
                ? (int) round(($completedDuration / $totalDuration) * 100)
                : 0;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'status_label' => $this->projectStatusLabel($project->status),
                'start_date' => optional($project->start_date)->toDateString(),
                'progress_percent' => max(0, min(100, $progressPercent)),
                'completed_tasks' => (int) ($summary?->completed_tasks ?? 0),
                'total_tasks' => (int) ($summary?->total_tasks ?? 0),
            ];
        })->values()->all();
    }

    private function buildAttendanceMonthReport(User $user, ?int $profileId): array
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $month = (int) $now->month;
        $year = (int) $now->year;

        $query = AttendanceRecord::query()
            ->whereMonth('work_date', $month)
            ->whereYear('work_date', $year);

        $scope = 'company';
        if (!AccessMatrix::canManageAllAttendance($user) && $profileId) {
            $query->where('employee_profile_id', $profileId);
            $scope = 'personal';
        }

        $report = (clone $query)
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw("SUM(CASE WHEN attendance_status IN ('on_time', 'present') THEN 1 ELSE 0 END) as on_time_records")
            ->selectRaw("SUM(CASE WHEN attendance_status = 'late' OR COALESCE(late_minutes, 0) > 0 THEN 1 ELSE 0 END) as late_records")
            ->selectRaw("SUM(CASE WHEN attendance_status = 'absent' THEN 1 ELSE 0 END) as absent_records")
            ->selectRaw("SUM(CASE WHEN day_status = 'early_leave' OR COALESCE(early_leave_minutes, 0) > 0 THEN 1 ELSE 0 END) as early_leave_records")
            ->selectRaw("SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved_records")
            ->selectRaw('SUM(COALESCE(worked_minutes, 0)) as total_worked_minutes')
            ->first();

        return [
            'scope' => $scope,
            'month' => $month,
            'year' => $year,
            'total_records' => (int) ($report?->total_records ?? 0),
            'on_time_records' => (int) ($report?->on_time_records ?? 0),
            'late_records' => (int) ($report?->late_records ?? 0),
            'absent_records' => (int) ($report?->absent_records ?? 0),
            'early_leave_records' => (int) ($report?->early_leave_records ?? 0),
            'approved_records' => (int) ($report?->approved_records ?? 0),
            'total_worked_minutes' => (int) ($report?->total_worked_minutes ?? 0),
        ];
    }

    private function projectStatusLabel(?string $status): string
    {
        return match ((string) $status) {
            'planning' => 'Ke hoach',
            'in_progress' => 'Dang trien khai',
            'on_hold' => 'Tam dung',
            'completed' => 'Hoan thanh',
            default => '-',
        };
    }

    private function hasGlobalProjectAccess(User $user): bool
    {
        return AccessMatrix::canViewAllProjects($user);
    }

    public function myProfile(Request $request): Response
    {
        $user = $request->user()->load([
            'employeeProfile.department:id,name',
            'employeeProfile.position',
            'employeeProfile.province:id,name',
            'employeeProfile.ward:id,name',
        ]);

        return Inertia::render('Profile/My', [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'status' => $user->status,
                'last_login_at' => optional($user->last_login_at)?->format('Y-m-d H:i:s'),
                'employee_code' => $user->employeeProfile?->employee_code,
                'hire_date' => optional($user->employeeProfile?->hire_date)?->format('Y-m-d'),
                'date_of_birth' => optional($user->employeeProfile?->date_of_birth)?->format('Y-m-d'),
                'employment_status' => $user->employeeProfile?->employment_status,
                'employment_type' => $user->employeeProfile?->employment_type,
                'department' => $user->employeeProfile?->department?->name,
                'position' => $user->employeeProfile?->position?->name,
                'authority_level' => (int) ($user->employeeProfile?->position?->authority_level ?? 0),
                'base_salary' => $user->employeeProfile?->base_salary,
                'province_id' => $user->employeeProfile?->province_id,
                'ward_id' => $user->employeeProfile?->ward_id,
                'province_name' => $user->employeeProfile?->province?->name,
                'ward_name' => $user->employeeProfile?->ward?->name,
                'address_line' => $user->employeeProfile?->address_line,
                'full_address' => collect([
                    $user->employeeProfile?->address_line,
                    $user->employeeProfile?->ward?->name,
                    $user->employeeProfile?->province?->name,
                ])->filter()->join(', '),
            ],
        ]);
    }

    public function myAttendance(Request $request): Response
    {
        $profileId = $request->user()->employeeProfile?->id;

        $records = AttendanceRecord::query()
            ->where('employee_profile_id', $profileId)
            ->orderByDesc('work_date')
            ->limit(30)
            ->get()
            ->map(fn (AttendanceRecord $record) => [
                'id' => $record->id,
                'work_date' => $record->work_date,
                'check_in_at' => $record->check_in_at,
                'check_out_at' => $record->check_out_at,
                'worked_minutes' => $record->worked_minutes,
                'attendance_status' => $record->attendance_status,
                'is_confirmed' => (bool) $record->is_confirmed,
            ])
            ->values();

        return Inertia::render('Attendance/My', [
            'records' => $records,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'province_id' => 'nullable|exists:provinces,id',
            'ward_id' => 'nullable|exists:wards,id',
            'address_line' => 'nullable|string|max:255',
        ]);

        $user->update([
            'phone' => $request->phone,
        ]);

        $user->employeeProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'employee_code' => $user->employeeProfile?->employee_code ?? ('EMP-' . str_pad($user->id, 5, '0', STR_PAD_LEFT)),
                'date_of_birth' => $request->date_of_birth,
                'hire_date' => $user->employeeProfile?->hire_date ?? now(),
                'province_id' => $request->province_id,
                'ward_id' => $request->ward_id,
                'address_line' => $request->address_line,
                'employment_status' => $user->employeeProfile?->employment_status ?? 'active',
                'employment_type' => $user->employeeProfile?->employment_type ?? 'official',
            ]
        );

        return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function updateAvatar(Request $request)
    {
        Log::info('Avatar upload started', ['user_id' => $request->user()->id]);

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp|max:5120',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh đại diện.',
            'avatar.image'    => 'Tệp tải lên phải là hình ảnh.',
            'avatar.mimes'    => 'Ảnh đại diện hỗ trợ các định dạng: jpeg, png, jpg, gif, webp, bmp.',
            'avatar.max'      => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            Log::info('File detected', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);

            // Delete old avatar if exists
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }

            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'avatars/' . $filename;

            try {
                if (extension_loaded('gd')) {
                    // Resize and save image using Intervention (preferred)
                    $image = Image::read($file);
                    $image->cover(400, 400);
                    Storage::disk('public')->put($path, $image->toJpeg()->toString());
                    Log::info('Image processed and saved with Intervention');
                } else {
                    throw new \Exception('GD extension not loaded');
                }
            } catch (\Exception $e) {
                Log::warning('Intervention failed, falling back to raw save', ['error' => $e->getMessage()]);
                // Fallback: save original file if GD/Imagick driver is missing
                Storage::disk('public')->putFileAs('avatars', $file, $filename);
            }

            $user->update([
                'avatar' => '/storage/' . $path,
            ]);

            Log::info('Avatar updated successfully', ['path' => $path]);
        } else {
            Log::error('No file detected in request');
        }

        return redirect()->back()->with('success', 'Cập nhật ảnh đại diện thành công.');
    }
}
