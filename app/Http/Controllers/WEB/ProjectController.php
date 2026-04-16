<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\Project;
use App\Models\ProjectDetailLog;
use App\Models\ProjectImplementationDetail;
use App\Models\ProjectMember;
use App\Models\ProjectProgressHistory;
use App\Models\ProjectRole;
use App\Support\AccessMatrix;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    private const STATUSES = ['planning', 'in_progress', 'on_hold', 'completed'];
    private const DETAIL_STATUSES = ['planned', 'in_progress', 'completed', 'cancelled'];

    public function index(Request $request): Response
    {
        abort_unless($this->canViewAllProjects($request->user()), 403);

        return $this->renderProjectsPage($request, 'all');
    }

    public function myProjects(Request $request): Response
    {
        abort_unless($this->canViewOwnProjects($request->user()), 403);

        return $this->renderProjectsPage($request, 'mine');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canManageProjects($request->user()), 403);

        $validated = $this->validateProjectPayload($request);

        DB::transaction(function () use ($request, $validated): void {
            $project = Project::query()->create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
                'created_by' => $request->user()?->id,
                'updated_by' => $request->user()?->id,
            ]);

            $this->syncMembers($project, $validated['members'] ?? []);
            $this->recordStatusHistory($project, null, $validated['status'], $request->user()?->id, 'Tao moi du an');
        });

        return redirect()->back()->with('success', 'Da tao du an moi thanh cong.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageProjects($request->user()), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the cap nhat.',
            ]);
        }

        $validated = $this->validateProjectPayload($request, $project->id);

        DB::transaction(function () use ($request, $project, $validated): void {
            $oldStatus = (string) $project->status;

            $project->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
                'updated_by' => $request->user()?->id,
            ]);

            $this->syncMembers($project->fresh(), $validated['members'] ?? []);

            if ($oldStatus !== $validated['status']) {
                $this->recordStatusHistory($project, $oldStatus, $validated['status'], $request->user()?->id, 'Cap nhat trang thai du an');
            }
        });

        return redirect()->back()->with('success', 'Da cap nhat du an thanh cong.');
    }

    public function toggleLock(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageProjects($request->user()), 403);

        $isLocked = (bool) $project->is_locked;

        $project->update([
            'is_locked' => !$isLocked,
            'locked_at' => $isLocked ? null : now(),
            'locked_by' => $isLocked ? null : $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', $isLocked
            ? 'Da mo khoa du an thanh cong.'
            : 'Da khoa du an thanh cong.');
    }

    public function addMember(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageProjectMembers($request->user()), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the thay doi thanh vien.',
            ]);
        }

        $validated = $request->validate([
            'employee_profile_id' => ['required', 'integer', 'exists:employee_profiles,id'],
            'role_name' => ['required', 'string', 'max:100'],
            'joined_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request, $project, $validated): void {
            $role = $this->resolveProjectRole($project, (string) $validated['role_name']);

            $member = ProjectMember::query()->where('project_id', $project->id)
                ->where('employee_profile_id', (int) $validated['employee_profile_id'])
                ->first();

            if ($member) {
                $member->update([
                    'project_role_id' => $role->id,
                    'joined_at' => $validated['joined_at'] ?? $member->joined_at ?? $project->start_date,
                    'left_at' => null,
                    'is_active' => true,
                ]);
            } else {
                ProjectMember::query()->create([
                    'project_id' => $project->id,
                    'employee_profile_id' => (int) $validated['employee_profile_id'],
                    'project_role_id' => $role->id,
                    'joined_at' => $validated['joined_at'] ?? $project->start_date,
                    'is_active' => true,
                ]);
            }

            $project->update([
                'updated_by' => $request->user()?->id,
            ]);
        });

        return redirect()->back()->with('success', 'Da them nhan su vao du an.');
    }

    public function updateMemberRole(Request $request, Project $project, ProjectMember $projectMember): RedirectResponse
    {
        abort_unless($this->canManageProjectMembers($request->user()), 403);

        if ($projectMember->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the thay doi thanh vien.',
            ]);
        }

        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($request, $project, $projectMember, $validated): void {
            $role = $this->resolveProjectRole($project, (string) $validated['role_name']);

            $projectMember->update([
                'project_role_id' => $role->id,
                'is_active' => true,
                'left_at' => null,
            ]);

            $project->update([
                'updated_by' => $request->user()?->id,
            ]);
        });

        return redirect()->back()->with('success', 'Da cap nhat vai tro nhan su trong du an.');
    }

    public function removeMember(Request $request, Project $project, ProjectMember $projectMember): RedirectResponse
    {
        abort_unless($this->canManageProjectMembers($request->user()), 403);

        if ($projectMember->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the thay doi thanh vien.',
            ]);
        }

        $projectMember->update([
            'is_active' => false,
            'left_at' => now()->toDateString(),
        ]);

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Da loai nhan su khoi du an.');
    }

    public function addRole(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageProjectRoles($request->user()), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the thay doi vai tro.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Ten vai tro la bat buoc.',
            'name.max' => 'Ten vai tro khong duoc qua 100 ky tu.',
        ]);

        $roleName = trim((string) $validated['name']);

        $exists = ProjectRole::query()
            ->where('project_id', $project->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($roleName)])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'role' => 'Vai tro nay da ton tai trong du an.',
            ]);
        }

        ProjectRole::query()->create([
            'project_id' => $project->id,
            'name' => $roleName,
            'description' => $validated['description'] ?? null,
        ]);

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Da them vai tro du an.');
    }

    public function removeRole(Request $request, Project $project, ProjectRole $projectRole): RedirectResponse
    {
        abort_unless($this->canManageProjectRoles($request->user()), 403);

        if ($projectRole->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the thay doi vai tro.',
            ]);
        }

        $isInUse = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('project_role_id', $projectRole->id)
            ->where('is_active', true)
            ->exists();

        if ($isInUse) {
            return redirect()->back()->withErrors([
                'role' => 'Khong the xoa vai tro dang duoc gan cho thanh vien.',
            ]);
        }

        $projectRole->delete();

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Da xoa vai tro du an.');
    }

    public function storeImplementationDetail(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageImplementationDetails($request->user()), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the them dau viec.',
            ]);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'execution_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'detail_status' => ['nullable', Rule::in(self::DETAIL_STATUSES)],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ], [
            'content.required' => 'Noi dung cong viec la bat buoc.',
            'execution_date.required' => 'Ngay thuc hien la bat buoc.',
            'duration_days.required' => 'So ngay thuc hien la bat buoc.',
        ]);

        $assigneeId = $validated['assigned_to'] ?? null;
        if ($assigneeId) {
            $this->ensureEmployeeIsProjectMember($project, (int) $assigneeId);
        }

        $durationDays = (int) $validated['duration_days'];
        $executionDate = (string) $validated['execution_date'];
        $expectedEndDate = $this->calculateExpectedEndDate($executionDate, $durationDays);
        $oldProgress = $this->currentProjectProgressPercent($project);

        $status = (string) ($validated['detail_status'] ?? 'planned');
        $progressPercent = $this->normalizeProgressByStatus((int) ($validated['progress_percent'] ?? 0), $status);

        $detail = ProjectImplementationDetail::query()->create([
            'project_id' => $project->id,
            'assigned_to' => $assigneeId ? (int) $assigneeId : null,
            'content' => trim((string) $validated['content']),
            'execution_date' => $executionDate,
            'duration_days' => $durationDays,
            'expected_end_date' => $expectedEndDate,
            'detail_status' => $status,
            'progress_percent' => $progressPercent,
            'actual_end_date' => $status === 'completed' ? now()->toDateString() : null,
            'is_locked' => false,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $this->logImplementationChanges($detail, [
            'content' => [null, $detail->content],
            'assigned_to' => [null, $detail->assigned_to],
            'execution_date' => [null, $detail->execution_date?->toDateString()],
            'duration_days' => [null, $detail->duration_days],
            'expected_end_date' => [null, $detail->expected_end_date?->toDateString()],
            'detail_status' => [null, $detail->detail_status],
            'progress_percent' => [null, $detail->progress_percent],
        ], $request->user()?->id);

        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Them dau viec trien khai');

        return redirect()->back()->with('success', 'Da them dau viec trien khai.');
    }

    public function updateImplementationDetail(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canManageImplementationDetails($request->user()), 403);

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'detail' => 'Dau viec dang bi khoa, khong the cap nhat.',
            ]);
        }

        $isAdmin = $this->isAdmin($request->user());

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'execution_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'detail_status' => ['required', Rule::in(self::DETAIL_STATUSES)],
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        if (!$isAdmin && (
            (string) $validated['execution_date'] !== $implementationDetail->execution_date?->toDateString()
            || (int) $validated['duration_days'] !== (int) $implementationDetail->duration_days
        )) {
            return redirect()->back()->withErrors([
                'detail' => 'Chi admin moi duoc thay doi thoi gian thuc hien.',
            ]);
        }

        $assigneeId = $validated['assigned_to'] ?? null;
        if ($assigneeId) {
            $this->ensureEmployeeIsProjectMember($project, (int) $assigneeId);
        }

        $durationDays = (int) $validated['duration_days'];
        $executionDate = (string) $validated['execution_date'];
        $expectedEndDate = $this->calculateExpectedEndDate($executionDate, $durationDays);
        $status = (string) $validated['detail_status'];
        $progressPercent = $this->normalizeProgressByStatus((int) $validated['progress_percent'], $status);
        $oldProgress = $this->currentProjectProgressPercent($project);

        $before = [
            'content' => $implementationDetail->content,
            'assigned_to' => $implementationDetail->assigned_to,
            'execution_date' => $implementationDetail->execution_date?->toDateString(),
            'duration_days' => (int) $implementationDetail->duration_days,
            'expected_end_date' => $implementationDetail->expected_end_date?->toDateString(),
            'detail_status' => $implementationDetail->detail_status,
            'progress_percent' => (int) $implementationDetail->progress_percent,
        ];

        $implementationDetail->update([
            'content' => trim((string) $validated['content']),
            'assigned_to' => $assigneeId ? (int) $assigneeId : null,
            'execution_date' => $executionDate,
            'duration_days' => $durationDays,
            'expected_end_date' => $expectedEndDate,
            'detail_status' => $status,
            'progress_percent' => $progressPercent,
            'actual_end_date' => $status === 'completed' ? ($implementationDetail->actual_end_date ?? now()->toDateString()) : null,
            'updated_by' => $request->user()?->id,
        ]);

        $after = [
            'content' => $implementationDetail->content,
            'assigned_to' => $implementationDetail->assigned_to,
            'execution_date' => $implementationDetail->execution_date?->toDateString(),
            'duration_days' => (int) $implementationDetail->duration_days,
            'expected_end_date' => $implementationDetail->expected_end_date?->toDateString(),
            'detail_status' => $implementationDetail->detail_status,
            'progress_percent' => (int) $implementationDetail->progress_percent,
        ];

        $this->logDiffToDetailLogs($implementationDetail, $before, $after, $request->user()?->id);

        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Cap nhat dau viec trien khai');

        return redirect()->back()->with('success', 'Da cap nhat dau viec trien khai.');
    }

    public function updateImplementationDetailStatus(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        if (!$this->canUpdateImplementationStatus($request->user(), $implementationDetail)) {
            abort(403);
        }

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'detail' => 'Dau viec dang bi khoa, khong the cap nhat trang thai.',
            ]);
        }

        $validated = $request->validate([
            'detail_status' => ['required', Rule::in(self::DETAIL_STATUSES)],
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $before = [
            'detail_status' => $implementationDetail->detail_status,
            'progress_percent' => (int) $implementationDetail->progress_percent,
        ];
        $oldProgress = $this->currentProjectProgressPercent($project);

        $status = (string) $validated['detail_status'];
        $progressPercent = $this->normalizeProgressByStatus((int) $validated['progress_percent'], $status);

        $implementationDetail->update([
            'detail_status' => $status,
            'progress_percent' => $progressPercent,
            'actual_end_date' => $status === 'completed' ? ($implementationDetail->actual_end_date ?? now()->toDateString()) : null,
            'updated_by' => $request->user()?->id,
        ]);

        $after = [
            'detail_status' => $implementationDetail->detail_status,
            'progress_percent' => (int) $implementationDetail->progress_percent,
        ];

        $this->logDiffToDetailLogs($implementationDetail, $before, $after, $request->user()?->id);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Cap nhat trang thai dau viec');

        return redirect()->back()->with('success', 'Da cap nhat trang thai dau viec.');
    }

    public function toggleImplementationDetailLock(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canManageImplementationDetails($request->user()), 403);

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Du an dang bi khoa, khong the thay doi trang thai khoa dau viec.',
            ]);
        }

        $before = ['is_locked' => (bool) $implementationDetail->is_locked];
        $oldProgress = $this->currentProjectProgressPercent($project);
        $implementationDetail->update([
            'is_locked' => !$implementationDetail->is_locked,
            'updated_by' => $request->user()?->id,
        ]);
        $after = ['is_locked' => (bool) $implementationDetail->is_locked];
        $this->logDiffToDetailLogs($implementationDetail, $before, $after, $request->user()?->id);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Thay doi khoa dau viec');

        return redirect()->back()->with('success', $implementationDetail->is_locked
            ? 'Da khoa dau viec.'
            : 'Da mo khoa dau viec.');
    }

    public function destroyImplementationDetail(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canManageImplementationDetails($request->user()), 403);

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'detail' => 'Dau viec dang bi khoa, khong the xoa.',
            ]);
        }

        $this->logImplementationChanges($implementationDetail, [
            'deleted' => ['0', '1'],
        ], $request->user()?->id);
        $oldProgress = $this->currentProjectProgressPercent($project);

        $implementationDetail->delete();

        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Xoa dau viec trien khai');

        return redirect()->back()->with('success', 'Da xoa dau viec trien khai.');
    }

    private function renderProjectsPage(Request $request, string $scope): Response
    {
        $filters = [
            'search' => trim((string) $request->string('search')),
            'status' => (string) $request->string('status'),
            'employee_profile_id' => (int) $request->integer('employee_profile_id'),
        ];

        $query = $this->baseProjectQuery($request, $scope);

        if ($filters['search'] !== '') {
            $keyword = $filters['search'];
            $query->where(function (Builder $builder) use ($keyword): void {
                $builder
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['employee_profile_id'] > 0) {
            $query->whereHas('members', function (Builder $builder) use ($filters): void {
                $builder
                    ->where('employee_profile_id', $filters['employee_profile_id'])
                    ->where('is_active', true);
            });
        }

        $pageUser = $request->user();

        $projects = $query
            ->with([
                'members' => function ($relation): void {
                    $relation
                        ->where('is_active', true)
                        ->with([
                            'employeeProfile.user:id,name',
                            'employeeProfile.position:id,name',
                            'role:id,name',
                        ])
                        ->orderBy('id');
                },
                'roles:id,project_id,name',
                'progressHistories' => fn ($relation) => $relation->with('changer:id,name')->latest('changed_at')->limit(30),
                'implementationDetails' => fn ($relation) => $relation
                    ->with([
                        'assignee.user:id,name',
                        'logs.updatedBy:id,name',
                    ])
                    ->orderByDesc('id'),
            ])
            ->withCount([
                'members as active_members_count' => fn ($relation) => $relation->where('is_active', true),
            ])
            ->orderByDesc('id')
            ->get()
            ->map(fn (Project $project) => $this->transformProject($project, $pageUser))
            ->values();

        $employeeOptions = EmployeeProfile::query()
            ->where('employment_status', 'active')
            ->whereHas('user', fn (Builder $builder) => $builder->where('status', 'active'))
            ->with(['user:id,name', 'position:id,name'])
            ->orderBy('employee_code')
            ->get()
            ->map(fn (EmployeeProfile $profile) => [
                'id' => $profile->id,
                'employee_code' => $profile->employee_code,
                'name' => $profile->user?->name,
                'position_name' => $profile->position?->name,
                'label' => trim(($profile->employee_code ? ($profile->employee_code . ' - ') : '') . ($profile->user?->name ?? 'Nhan su')),
            ])
            ->values();

        $employeeProjectOverview = ProjectMember::query()
            ->where('is_active', true)
            ->with([
                'employeeProfile.user:id,name',
                'project:id,name,status',
                'role:id,name',
            ])
            ->get()
            ->groupBy('employee_profile_id')
            ->map(function ($rows, $employeeProfileId) {
                $first = $rows->first();

                return [
                    'employee_profile_id' => (int) $employeeProfileId,
                    'employee_name' => $first?->employeeProfile?->user?->name,
                    'employee_code' => $first?->employeeProfile?->employee_code,
                    'project_count' => $rows->count(),
                    'projects' => $rows->map(fn (ProjectMember $member) => [
                        'project_id' => $member->project_id,
                        'project_name' => $member->project?->name,
                        'project_status' => $member->project?->status,
                        'project_status_label' => $this->statusLabel($member->project?->status),
                        'role_name' => $member->role?->name,
                    ])->values(),
                ];
            })
            ->values();

        $canManageProjects = $this->canManageProjects($pageUser);
        $canManageMembers = $this->canManageProjectMembers($pageUser);
        $canManageProjectRoles = $this->canManageProjectRoles($pageUser);
        $canManageImplementationDetails = $this->canManageImplementationDetails($pageUser);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'scope' => $scope,
            'title' => $scope === 'mine' ? 'Du an cua toi' : 'Danh sach du an',
            'filters' => $filters,
            'status_options' => collect(self::STATUSES)
                ->map(fn (string $status) => ['value' => $status, 'label' => $this->statusLabel($status)])
                ->values(),
            'employee_options' => $employeeOptions,
            'project_role_options' => $this->projectRoleOptions(),
            'employee_project_overview' => $employeeProjectOverview,
            'can_manage_projects' => $canManageProjects,
            'can_manage_members' => $canManageMembers,
            'can_manage_project_roles' => $canManageProjectRoles,
            'can_manage_implementation_details' => $canManageImplementationDetails,
            'can_edit_implementation_schedule' => $this->isAdmin($pageUser),
            'implementation_status_options' => collect(self::DETAIL_STATUSES)
                ->map(fn (string $status) => ['value' => $status, 'label' => $this->implementationStatusLabel($status)])
                ->values(),
        ]);
    }

    private function baseProjectQuery(Request $request, string $scope): Builder
    {
        $query = Project::query();

        if ($scope === 'all') {
            return $query;
        }

        $user = $request->user();
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $profileId = $user->employeeProfile?->id ?? 0;
        if ($profileId <= 0) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('members', function (Builder $builder) use ($profileId): void {
            $builder
                ->where('employee_profile_id', $profileId)
                ->where('is_active', true);
        });
    }

    private function validateProjectPayload(Request $request, ?int $projectId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(fn ($query) => $query->where('start_date', $request->input('start_date')))->ignore($projectId),
            ],
            'start_date' => ['required', 'date'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'description' => ['nullable', 'string'],
            'members' => ['nullable', 'array'],
            'members.*.employee_profile_id' => ['required', 'integer', 'distinct', 'exists:employee_profiles,id'],
            'members.*.role_name' => ['required', 'string', 'max:100'],
            'members.*.joined_at' => ['nullable', 'date'],
        ], [
            'name.required' => 'Ten du an la bat buoc.',
            'name.unique' => 'Da ton tai du an cung ten va ngay bat dau.',
            'start_date.required' => 'Ngay bat dau la bat buoc.',
            'status.required' => 'Trang thai la bat buoc.',
            'status.in' => 'Trang thai khong hop le.',
            'members.*.employee_profile_id.required' => 'Vui long chon nhan su tham gia.',
            'members.*.employee_profile_id.distinct' => 'Nhan su bi trung trong danh sach.',
            'members.*.employee_profile_id.exists' => 'Nhan su khong hop le.',
            'members.*.role_name.required' => 'Vai tro trong du an la bat buoc.',
        ]);
    }

    private function syncMembers(Project $project, array $members): void
    {
        $existingMembers = ProjectMember::query()
            ->where('project_id', $project->id)
            ->get()
            ->keyBy('employee_profile_id');

        $currentProfileIds = [];

        foreach ($members as $member) {
            $profileId = (int) $member['employee_profile_id'];
            $roleName = trim((string) $member['role_name']);

            if ($profileId <= 0 || $roleName === '') {
                continue;
            }

            $currentProfileIds[] = $profileId;

            $projectRole = $this->resolveProjectRole($project, $roleName);

            $record = $existingMembers->get($profileId);
            if ($record) {
                $record->update([
                    'project_role_id' => $projectRole->id,
                    'joined_at' => $member['joined_at'] ?? $record->joined_at ?? $project->start_date,
                    'left_at' => null,
                    'is_active' => true,
                ]);

                continue;
            }

            ProjectMember::query()->create([
                'project_id' => $project->id,
                'employee_profile_id' => $profileId,
                'project_role_id' => $projectRole->id,
                'joined_at' => $member['joined_at'] ?? $project->start_date,
                'is_active' => true,
            ]);
        }

        ProjectMember::query()
            ->where('project_id', $project->id)
            ->when(!empty($currentProfileIds), fn (Builder $builder) => $builder->whereNotIn('employee_profile_id', $currentProfileIds))
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'left_at' => now()->toDateString(),
            ]);
    }

    private function resolveProjectRole(Project $project, string $roleName): ProjectRole
    {
        $normalizedRoleName = trim($roleName);

        $role = ProjectRole::query()
            ->where('project_id', $project->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedRoleName)])
            ->first();

        if ($role) {
            return $role;
        }

        throw ValidationException::withMessages([
            'role_name' => 'Vai tro khong hop le. Vui long chon vai tro da duoc tao boi quan tri du an.',
        ]);
    }

    private function recordStatusHistory(Project $project, ?string $oldStatus, string $newStatus, ?int $changedBy, ?string $note = null): void
    {
        ProjectProgressHistory::query()->create([
            'project_id' => $project->id,
            'old_progress' => $this->statusToProgress($oldStatus),
            'new_progress' => $this->statusToProgress($newStatus),
            'changed_at' => now(),
            'changed_by' => $changedBy,
            'note' => $note ?: sprintf('Trang thai: %s -> %s', $this->statusLabel($oldStatus), $this->statusLabel($newStatus)),
        ]);
    }

    private function transformProject(Project $project, $pageUser): array
    {
        $visibleImplementationDetails = $this->visibleImplementationDetails($project, $pageUser);
        $progressSummary = $this->buildProjectProgressSummary($project->implementationDetails);

        return [
            'id' => $project->id,
            'name' => $project->name,
            'status' => $project->status,
            'status_label' => $this->statusLabel($project->status),
            'progress_percent' => $progressSummary['progress_percent'],
            'task_summary' => [
                'total' => $progressSummary['total_tasks'],
                'completed' => $progressSummary['completed_tasks'],
                'incomplete' => $progressSummary['incomplete_tasks'],
                'delayed' => $progressSummary['delayed_tasks'],
            ],
            'is_delayed' => $progressSummary['delayed_tasks'] > 0,
            'delay_warning' => $progressSummary['delayed_tasks'] > 0
                ? sprintf('Du an co %d dau viec cham tien do.', $progressSummary['delayed_tasks'])
                : null,
            'start_date' => optional($project->start_date)->format('Y-m-d'),
            'description' => $project->description,
            'is_locked' => (bool) $project->is_locked,
            'active_members_count' => (int) ($project->active_members_count ?? 0),
            'members' => $project->members->map(fn (ProjectMember $member) => [
                'id' => $member->id,
                'employee_profile_id' => $member->employee_profile_id,
                'employee_name' => $member->employeeProfile?->user?->name,
                'employee_code' => $member->employeeProfile?->employee_code,
                'position_name' => $member->employeeProfile?->position?->name,
                'role_name' => $member->role?->name,
                'joined_at' => optional($member->joined_at)->format('Y-m-d'),
            ])->values(),
            'roles' => $project->roles->map(fn (ProjectRole $role) => [
                'id' => $role->id,
                'name' => $role->name,
            ])->values(),
            'role_options' => $this->projectRoleOptions($project),
            'status_histories' => $project->progressHistories->map(fn (ProjectProgressHistory $history) => [
                'id' => $history->id,
                'old_progress' => (int) $history->old_progress,
                'new_progress' => (int) $history->new_progress,
                'changed_at' => optional($history->changed_at)->format('Y-m-d H:i:s'),
                'changed_by_name' => $history->changer?->name,
                'note' => $history->note,
            ])->values(),
            'implementation_details' => $visibleImplementationDetails->map(function (ProjectImplementationDetail $detail) use ($pageUser) {
                $canUpdateStatus = $this->canUpdateImplementationStatus($pageUser, $detail);

                return [
                    'id' => $detail->id,
                    'content' => $detail->content,
                    'assigned_to' => $detail->assigned_to,
                    'assigned_name' => $detail->assignee?->user?->name,
                    'assigned_code' => $detail->assignee?->employee_code,
                    'execution_date' => optional($detail->execution_date)->format('Y-m-d'),
                    'duration_days' => (int) $detail->duration_days,
                    'expected_end_date' => optional($detail->expected_end_date)->format('Y-m-d'),
                    'actual_end_date' => optional($detail->actual_end_date)->format('Y-m-d'),
                    'detail_status' => $detail->detail_status,
                    'detail_status_label' => $this->implementationStatusLabel($detail->detail_status),
                    'progress_percent' => (int) ($detail->progress_percent ?? 0),
                    'is_locked' => (bool) $detail->is_locked,
                    'can_update_status' => $canUpdateStatus && !$detail->is_locked,
                    'logs' => $detail->logs
                        ->sortByDesc('id')
                        ->take(20)
                        ->values()
                        ->map(fn (ProjectDetailLog $log) => [
                            'id' => $log->id,
                            'field_name' => $log->field_name,
                            'field_label' => $this->detailLogFieldLabel($log->field_name),
                            'old_value' => $log->old_value,
                            'new_value' => $log->new_value,
                            'updated_by_name' => $log->updatedBy?->name,
                            'updated_at' => optional($log->created_at)->format('Y-m-d H:i:s'),
                        ]),
                ];
            })->values(),
        ];
    }

    private function projectRoleOptions(?Project $project = null): array
    {
        if ($project) {
            $names = $project->roles->pluck('name');
        } else {
            $names = ProjectRole::query()
                ->select('name')
                ->distinct()
                ->pluck('name');
        }

        return $names
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique(fn (string $name) => mb_strtolower($name))
            ->sort()
            ->values()
            ->map(fn (string $name) => [
                'value' => $name,
                'label' => $name,
            ])
            ->all();
    }

    private function visibleImplementationDetails(Project $project, $user)
    {
        if ($this->canManageImplementationDetails($user)) {
            return $project->implementationDetails;
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return collect();
        }

        return $project->implementationDetails
            ->where('assigned_to', (int) $profileId)
            ->values();
    }

    private function ensureEmployeeIsProjectMember(Project $project, int $employeeProfileId): void
    {
        $isMember = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('employee_profile_id', $employeeProfileId)
            ->where('is_active', true)
            ->exists();

        if (!$isMember) {
            throw ValidationException::withMessages([
                'assigned_to' => 'Nhan su duoc giao phai la thanh vien dang hoat dong cua du an.',
            ]);
        }
    }

    private function calculateExpectedEndDate(string $executionDate, int $durationDays): string
    {
        return Carbon::parse($executionDate)
            ->addDays(max(1, $durationDays) - 1)
            ->toDateString();
    }

    private function normalizeProgressByStatus(int $progressPercent, string $status): int
    {
        $progress = max(0, min(100, $progressPercent));

        if ($status === 'planned' && $progress > 0) {
            return 0;
        }

        if ($status === 'completed') {
            return 100;
        }

        return $progress;
    }

    private function currentProjectProgressPercent(Project $project): int
    {
        $details = ProjectImplementationDetail::query()
            ->where('project_id', $project->id)
            ->get();

        return $this->buildProjectProgressSummary($details)['progress_percent'];
    }

    private function recordImplementationProgressHistory(Project $project, int $oldProgress, ?int $changedBy, string $action): void
    {
        $newProgress = $this->currentProjectProgressPercent($project);
        if ($newProgress === $oldProgress) {
            return;
        }

        ProjectProgressHistory::query()->create([
            'project_id' => $project->id,
            'old_progress' => $oldProgress,
            'new_progress' => $newProgress,
            'changed_at' => now(),
            'changed_by' => $changedBy,
            'note' => sprintf('%s: %d%% -> %d%% (theo trong so so ngay dau viec)', $action, $oldProgress, $newProgress),
        ]);
    }

    private function buildProjectProgressSummary(Collection $details): array
    {
        $activeDetails = $details
            ->filter(fn (ProjectImplementationDetail $detail) => $detail->detail_status !== 'cancelled')
            ->values();

        $totalTasks = $activeDetails->count();
        $completedTasks = (int) $activeDetails
            ->filter(fn (ProjectImplementationDetail $detail) => $detail->detail_status === 'completed')
            ->count();
        $incompleteTasks = max(0, $totalTasks - $completedTasks);
        $today = now()->startOfDay();
        $delayedTasks = (int) $activeDetails
            ->filter(function (ProjectImplementationDetail $detail) use ($today) {
                if ($detail->detail_status === 'completed') {
                    return false;
                }

                if (!$detail->expected_end_date) {
                    return false;
                }

                return Carbon::parse($detail->expected_end_date)->startOfDay()->lt($today);
            })
            ->count();

        if ($activeDetails->isEmpty()) {
            return [
                'progress_percent' => 0,
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'incomplete_tasks' => 0,
                'delayed_tasks' => 0,
            ];
        }

        $totalDuration = (int) $activeDetails->sum(fn (ProjectImplementationDetail $detail) => max(1, (int) $detail->duration_days));
        $completedDuration = (int) $activeDetails
            ->filter(fn (ProjectImplementationDetail $detail) => $detail->detail_status === 'completed')
            ->sum(fn (ProjectImplementationDetail $detail) => max(1, (int) $detail->duration_days));
        $progressPercent = $totalDuration > 0
            ? (int) round(($completedDuration / $totalDuration) * 100)
            : 0;

        return [
            'progress_percent' => max(0, min(100, $progressPercent)),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'incomplete_tasks' => $incompleteTasks,
            'delayed_tasks' => $delayedTasks,
        ];
    }

    private function logImplementationChanges(ProjectImplementationDetail $detail, array $changes, ?int $userId): void
    {
        foreach ($changes as $fieldName => [$oldValue, $newValue]) {
            ProjectDetailLog::query()->create([
                'implementation_detail_id' => $detail->id,
                'field_name' => $fieldName,
                'old_value' => $oldValue !== null ? (string) $oldValue : null,
                'new_value' => $newValue !== null ? (string) $newValue : null,
                'updated_by' => $userId,
            ]);
        }
    }

    private function logDiffToDetailLogs(ProjectImplementationDetail $detail, array $before, array $after, ?int $userId): void
    {
        $changes = [];
        foreach ($after as $field => $newValue) {
            $oldValue = $before[$field] ?? null;
            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            $changes[$field] = [$oldValue, $newValue];
        }

        if (empty($changes)) {
            return;
        }

        $this->logImplementationChanges($detail, $changes, $userId);
    }

    private function implementationStatusLabel(?string $status): string
    {
        return match ($status) {
            'planned' => 'Chua lam',
            'in_progress' => 'Dang lam',
            'completed' => 'Hoan thanh',
            'cancelled' => 'Tam dung',
            default => '-',
        };
    }

    private function detailLogFieldLabel(?string $field): string
    {
        return match ($field) {
            'content' => 'Noi dung',
            'assigned_to' => 'Nhan su',
            'execution_date' => 'Ngay thuc hien',
            'duration_days' => 'So ngay',
            'expected_end_date' => 'Ngay hoan thanh du kien',
            'detail_status' => 'Trang thai',
            'progress_percent' => 'Tien do',
            'is_locked' => 'Khoa dau viec',
            'deleted' => 'Xoa dau viec',
            default => $field ?? '-',
        };
    }

    private function statusToProgress(?string $status): int
    {
        return match ($status) {
            'planning' => 10,
            'in_progress' => 50,
            'on_hold' => 50,
            'completed' => 100,
            default => 0,
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'planning' => 'Ke hoach',
            'in_progress' => 'Dang trien khai',
            'on_hold' => 'Tam dung',
            'completed' => 'Hoan thanh',
            default => '-',
        };
    }

    private function canViewAllProjects($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasPositionCapability('view_all_projects')) {
            return true;
        }

        return $this->canManageProjects($user);
    }

    private function canManageProjects($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasPositionCapability('manage_projects')) {
            return true;
        }

        return (bool) ($user->employeeProfile?->is_department_head ?? false);
    }

    private function canManageProjectMembers($user): bool
    {
        if ($this->canManageProjects($user)) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $user->hasPositionCapability('manage_project_members');
    }

    private function canManageProjectRoles($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasPositionCapability('manage_project_roles');
    }

    private function canManageImplementationDetails($user): bool
    {
        return $this->canManageProjectMembers($user);
    }

    private function canViewOwnProjects($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->canViewAllProjects($user)) {
            return true;
        }

        return $user->hasActiveProjectMembership();
    }

    private function canUpdateImplementationStatus($user, ProjectImplementationDetail $detail): bool
    {
        if ($this->canManageImplementationDetails($user)) {
            return true;
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return (int) $detail->assigned_to === (int) $profileId;
    }

    private function isAdmin($user): bool
    {
        return (bool) $user?->hasPositionCapability('manage_projects');
    }
}
