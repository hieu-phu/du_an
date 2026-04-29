<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectDetailComment;
use App\Models\ProjectDetailLog;
use App\Models\ProjectImplementationDetail;
use App\Models\ProjectImplementationSubtask;
use App\Models\ProjectMember;
use App\Models\ProjectMilestone;
use App\Models\ProjectProgressHistory;
use App\Models\ProjectRole;
use App\Models\ProjectWorkLog;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\AccessMatrix;
use App\Support\PositionCapability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProjectController extends Controller
{
    private const STATUSES = ['planning', 'in_progress', 'on_hold', 'completed'];
    private const DEFAULT_PROJECT_STATUS = 'in_progress';
    private const STATUS_TRANSITIONS = [
        'planning' => ['in_progress', 'on_hold'],
        'in_progress' => ['on_hold', 'completed'],
        'on_hold' => ['in_progress'],
        'completed' => [],
    ];
    private const DETAIL_STATUSES = ['planned', 'in_progress', 'completed', 'cancelled'];
    private const DETAIL_STATUS_TRANSITIONS = [
        'planned' => ['in_progress'],
        'in_progress' => ['completed', 'cancelled'],
        'cancelled' => ['in_progress'],
        'completed' => [],
    ];
    private const PROJECT_ROLE_PERMISSIONS = [
        'add_project_member',
        'remove_project_member',
        'create_implementation_detail',
        'update_implementation_detail',
        'delete_implementation_detail',
        'toggle_implementation_detail_lock',
        'edit_implementation_schedule',
        'update_implementation_status',
        'create_implementation_subtask',
        'update_implementation_subtask',
        'delete_implementation_subtask',
        'update_implementation_subtask_status',
        'upload_project_attachment',
        'upload_implementation_attachment',
        'delete_project_attachment',
    ];
    private const LEGACY_PROJECT_ROLE_PERMISSION_ALIASES = [
        'manage_members' => [
            'add_project_member',
            'remove_project_member',
        ],
        'manage_implementation_details' => [
            'create_implementation_detail',
            'update_implementation_detail',
            'delete_implementation_detail',
            'toggle_implementation_detail_lock',
            'edit_implementation_schedule',
            'create_implementation_subtask',
            'update_implementation_subtask',
            'delete_implementation_subtask',
            'update_implementation_subtask_status',
        ],
        'manage_attachments' => [
            'upload_project_attachment',
            'upload_implementation_attachment',
            'delete_project_attachment',
        ],
    ];

    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        if ($this->canViewAllProjects($user)) {
            return $this->renderProjectsPage($request, 'all');
        }

        abort_unless($this->canViewOwnProjects($user), 403);

        return $this->renderProjectsPage($request, 'mine');
    }

    public function myProjects(Request $request): Response
    {
        abort_unless($this->canViewOwnProjects($request->user()), 403);

        return $this->renderProjectsPage($request, 'mine');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canManageProjects($request->user()), 403);

        if (!$request->filled('status')) {
            $request->merge(['status' => self::DEFAULT_PROJECT_STATUS]);
        }

        $validated = $this->validateProjectPayload($request);

        DB::transaction(function () use ($request, $validated): void {
            $project = Project::query()->create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
                'created_by' => $request->user()?->id,
                'updated_by' => $request->user()?->id,
            ]);

            $this->syncMembers($project, $validated['members'] ?? [], $request->user());
            $this->recordStatusHistory($project, null, $validated['status'], $request->user()?->id, 'Tạo mới dự án');
        });

        return redirect()->back()->with('success', 'Đã tạo dự án mới thành công.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageProjects($request->user()), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể cập nhật.',
            ]);
        }

        $validated = $this->validateProjectPayload($request, $project->id);
        $this->ensureValidProjectStatusTransition((string) $project->status, (string) $validated['status']);

        DB::transaction(function () use ($request, $project, $validated): void {
            $oldStatus = (string) $project->status;

            $project->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
                'updated_by' => $request->user()?->id,
            ]);

            $freshProject = $project->fresh();
            $this->syncMembers($freshProject, $validated['members'] ?? [], $request->user());

            if ($oldStatus !== $validated['status']) {
                $this->recordStatusHistory($project, $oldStatus, $validated['status'], $request->user()?->id, 'Cập nhật trạng thái dự án');
                $this->notifyProjectMembersAboutStatusChange($freshProject, $oldStatus, $validated['status'], $request->user());
            }
        });

        return redirect()->back()->with('success', 'Đã cập nhật dự án thành công.');
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
            ? 'Đã mở khóa dự án thành công.'
            : 'Đã khóa dự án thành công.');
    }

    public function addMember(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canAddProjectMember($request->user(), $project), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi thành viên.',
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
                $wasActive = (bool) $member->is_active;
                $member->update([
                    'project_role_id' => $role->id,
                    'joined_at' => $validated['joined_at'] ?? $member->joined_at ?? $project->start_date,
                    'left_at' => null,
                    'is_active' => true,
                ]);
            } else {
                $wasActive = false;
                $member = ProjectMember::query()->create([
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

            if (!$wasActive) {
                $this->notifyProjectMemberAssigned($project, $member->fresh(['employeeProfile.user', 'role']), $request->user());
            }
        });

        return redirect()->back()->with('success', 'Đã thêm nhân sự vào dự án.');
    }

    public function updateMemberRole(Request $request, Project $project, ProjectMember $projectMember): RedirectResponse
    {
        abort_unless($this->canUpdateProjectMemberRole($request->user(), $project), 403);

        if ($projectMember->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi thành viên.',
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

        return redirect()->back()->with('success', 'Đã cập nhật vai trò nhân sự trong dự án.');
    }

    public function removeMember(Request $request, Project $project, ProjectMember $projectMember): RedirectResponse
    {
        abort_unless($this->canRemoveProjectMember($request->user(), $project), 403);

        if ($projectMember->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi thành viên.',
            ]);
        }

        $projectMember->update([
            'is_active' => false,
            'left_at' => now()->toDateString(),
        ]);

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Đã loại nhân sự khỏi dự án.');
    }

    public function addRole(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canManageProjectRoles($request->user()), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi vai trò.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($this->validProjectRolePermissions())],
        ], [
            'name.required' => 'Tên vai trò là bắt buộc.',
            'name.max' => 'Tên vai trò không được quá 100 ký tự.',
        ]);

        $roleName = trim((string) $validated['name']);

        $exists = ProjectRole::query()
            ->where('project_id', $project->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($roleName)])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'role' => 'Vai trò này đã tồn tại trong dự án.',
            ]);
        }

        ProjectRole::query()->create([
            'project_id' => $project->id,
            'name' => $roleName,
            'description' => $validated['description'] ?? null,
            'permissions' => $this->normalizeProjectRolePermissions($validated['permissions'] ?? []),
        ]);

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Đã thêm vai trò dự án.');
    }

    public function updateRole(Request $request, Project $project, ProjectRole $projectRole): RedirectResponse
    {
        abort_unless($this->canManageProjectRoles($request->user()), 403);

        if ($projectRole->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi vai trò.',
            ]);
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($this->validProjectRolePermissions())],
        ]);

        $projectRole->update([
            'permissions' => $this->normalizeProjectRolePermissions($validated['permissions'] ?? []),
        ]);

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật quyền vai trò dự án.');
    }

    public function removeRole(Request $request, Project $project, ProjectRole $projectRole): RedirectResponse
    {
        abort_unless($this->canManageProjectRoles($request->user()), 403);

        if ($projectRole->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi vai trò.',
            ]);
        }

        $isInUse = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('project_role_id', $projectRole->id)
            ->where('is_active', true)
            ->exists();

        if ($isInUse) {
            return redirect()->back()->withErrors([
                'role' => 'Không thể xóa vai trò đang được gán cho thành viên.',
            ]);
        }

        $projectRole->delete();

        $project->update([
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', 'Đã xóa vai trò dự án.');
    }

    public function storeMilestone(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canUpdateImplementationDetail($request->user(), $project), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'milestone' => 'Dự án đang bị khóa, không thể thêm mốc quan trọng.',
            ]);
        }

        $validated = $this->validateMilestonePayload($request);

        $milestone = ProjectMilestone::query()->create([
            'project_id' => $project->id,
            'phase_name' => trim((string) $validated['phase_name']),
            'name' => trim((string) $validated['name']),
            'description' => $validated['description'] ?? null,
            'planned_start_date' => $validated['planned_start_date'] ?? null,
            'planned_end_date' => $validated['planned_end_date'] ?? null,
            'completed_at' => ($validated['status'] ?? 'planned') === 'completed' ? now()->toDateString() : null,
            'status' => $validated['status'] ?? 'planned',
            'sort_order' => ((int) ProjectMilestone::query()->where('project_id', $project->id)->max('sort_order')) + 1,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $project->update(['updated_by' => $request->user()?->id]);
        $this->notifyProjectMembersAboutMilestoneChange($milestone->fresh('project'), $request->user(), 'created');

        return redirect()->back()->with('success', 'Đã thêm mốc quan trọng cho dự án.');
    }

    public function updateMilestone(Request $request, Project $project, ProjectMilestone $projectMilestone): RedirectResponse
    {
        abort_unless($this->canUpdateImplementationDetail($request->user(), $project), 403);
        $this->ensureMilestoneBelongsToProject($project, $projectMilestone);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'milestone' => 'Dự án đang bị khóa, không thể cập nhật mốc quan trọng.',
            ]);
        }

        $validated = $this->validateMilestonePayload($request);
        $status = (string) ($validated['status'] ?? $projectMilestone->status);

        $projectMilestone->update([
            'phase_name' => trim((string) $validated['phase_name']),
            'name' => trim((string) $validated['name']),
            'description' => $validated['description'] ?? null,
            'planned_start_date' => $validated['planned_start_date'] ?? null,
            'planned_end_date' => $validated['planned_end_date'] ?? null,
            'completed_at' => $status === 'completed' ? ($projectMilestone->completed_at ?? now()->toDateString()) : null,
            'status' => $status,
            'updated_by' => $request->user()?->id,
        ]);

        $project->update(['updated_by' => $request->user()?->id]);
        $this->notifyProjectMembersAboutMilestoneChange($projectMilestone->fresh('project'), $request->user(), 'updated');

        return redirect()->back()->with('success', 'Đã cập nhật mốc quan trọng.');
    }

    public function destroyMilestone(Request $request, Project $project, ProjectMilestone $projectMilestone): RedirectResponse
    {
        abort_unless($this->canDeleteImplementationDetail($request->user(), $project), 403);
        $this->ensureMilestoneBelongsToProject($project, $projectMilestone);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'milestone' => 'Dự án đang bị khóa, không thể xóa mốc quan trọng.',
            ]);
        }

        $projectMilestone->delete();
        $project->update(['updated_by' => $request->user()?->id]);

        return redirect()->back()->with('success', 'Đã xóa mốc quan trọng.');
    }

    public function storeImplementationDetail(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canCreateImplementationDetail($request->user(), $project), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thêm đầu việc.',
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
            'content.required' => 'Nội dung công việc là bắt buộc.',
            'execution_date.required' => 'Ngày thực hiện là bắt buộc.',
            'duration_days.required' => 'Số ngày thực hiện là bắt buộc.',
        ]);

        $assigneeId = $validated['assigned_to'] ?? null;
        if ($assigneeId) {
            $this->ensureEmployeeIsProjectMember($project, (int) $assigneeId);
        }

        $durationDays = (int) $validated['duration_days'];
        $executionDate = (string) $validated['execution_date'];
        $expectedEndDate = $this->calculateExpectedEndDate($executionDate, $durationDays);
        $this->ensureImplementationDetailWithinProjectDeadline($project, $executionDate, $expectedEndDate);
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
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Thêm đầu việc triển khai');
        $this->notifyImplementationDetailAssigned($detail->fresh(['project', 'assignee.user']), $request->user());

        return redirect()->back()->with('success', 'Đã thêm đầu việc triển khai.');
    }

    public function updateImplementationDetail(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canUpdateImplementationDetail($request->user(), $project), 403);

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'detail' => 'Đầu việc đang bị khóa, không thể cập nhật.',
            ]);
        }

        $canEditImplementationSchedule = $this->canEditImplementationSchedule($request->user(), $project);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'execution_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'detail_status' => ['required', Rule::in(self::DETAIL_STATUSES)],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        if (!$canEditImplementationSchedule && (
            (string) $validated['execution_date'] !== $implementationDetail->execution_date?->toDateString()
            || (int) $validated['duration_days'] !== (int) $implementationDetail->duration_days
        )) {
            return redirect()->back()->withErrors([
                'detail' => 'Chỉ người có quyền điều chỉnh lịch triển khai mới được thay đổi thời gian thực hiện.',
            ]);
        }

        $assigneeId = $validated['assigned_to'] ?? null;
        if ($assigneeId) {
            $this->ensureEmployeeIsProjectMember($project, (int) $assigneeId);
        }

        $durationDays = (int) $validated['duration_days'];
        $executionDate = (string) $validated['execution_date'];
        $expectedEndDate = $this->calculateExpectedEndDate($executionDate, $durationDays);
        $this->ensureImplementationDetailWithinProjectDeadline($project, $executionDate, $expectedEndDate);
        $this->ensureExistingSubtasksWithinDetailDeadline($implementationDetail, $executionDate, $expectedEndDate);
        $hasSubtasks = $implementationDetail->subtasks()->exists();
        $status = $hasSubtasks
            ? (string) $implementationDetail->detail_status
            : (string) $validated['detail_status'];
        if (!$hasSubtasks) {
            $this->ensureValidImplementationStatusTransition((string) $implementationDetail->detail_status, $status);
        }
        $progressPercent = $hasSubtasks
            ? (int) $implementationDetail->progress_percent
            : $this->normalizeProgressByStatus((int) ($validated['progress_percent'] ?? 0), $status);
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

        if ($hasSubtasks) {
            $this->syncImplementationDetailFromSubtasks($implementationDetail->fresh('subtasks'), $request->user()?->id);
            $implementationDetail->refresh();
        }

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
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Cập nhật đầu việc triển khai');
        $freshDetail = $implementationDetail->fresh(['project', 'assignee.user']);
        if ((int) ($before['assigned_to'] ?? 0) !== (int) ($after['assigned_to'] ?? 0)) {
            $this->notifyImplementationDetailAssigned($freshDetail, $request->user());
        } elseif ($before !== $after) {
            $this->notifyImplementationDetailUpdated($freshDetail, $request->user(), 'Đầu việc của bạn đã được cập nhật.');
        }

        return redirect()->back()->with('success', 'Đã cập nhật đầu việc triển khai.');
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
                'detail' => 'Đầu việc đang bị khóa, không thể cập nhật trạng thái.',
            ]);
        }

        if ($implementationDetail->subtasks()->exists()) {
            return redirect()->back()->withErrors([
                'detail' => 'Tiến độ và trạng thái đầu việc được tính từ công việc con.',
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
        $this->ensureValidImplementationStatusTransition((string) $implementationDetail->detail_status, $status);
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
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Cập nhật trạng thái đầu việc');
        if ($before !== $after) {
            $this->notifyImplementationDetailUpdated(
                $implementationDetail->fresh(['project', 'assignee.user']),
                $request->user(),
                sprintf(
                    'Trạng thái đầu việc đã đổi sang "%s" với tiến độ %d%%.',
                    $this->implementationStatusLabel($implementationDetail->detail_status),
                    (int) $implementationDetail->progress_percent
                )
            );
        }

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái đầu việc.');
    }

    public function storeImplementationSubtask(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canCreateImplementationSubtask($request->user(), $project), 403);
        $this->ensureImplementationDetailBelongsToProject($project, $implementationDetail);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'subtask' => 'Không thể thêm công việc con khi đầu việc đang khóa.',
            ]);
        }

        $validated = $this->validateImplementationSubtaskPayload($request);
        $assigneeId = $validated['assigned_to'] ?? null;
        if ($assigneeId) {
            $this->ensureEmployeeIsProjectMember($project, (int) $assigneeId);
        }

        $oldProgress = $this->currentProjectProgressPercent($project);
        $startDate = (string) $validated['start_date'];
        $dueDate = (string) $validated['due_date'];
        $this->ensureSubtaskWithinImplementationDetailDeadline($implementationDetail, $startDate, $dueDate);
        $durationDays = $this->calculateDurationDays($startDate, $dueDate);
        $status = 'planned';
        $progressPercent = 0;

        ProjectImplementationSubtask::query()->create([
            'project_implementation_detail_id' => $implementationDetail->id,
            'project_id' => $project->id,
            'assigned_to' => $assigneeId ? (int) $assigneeId : null,
            'title' => trim((string) $validated['title']),
            'description' => $validated['description'] ?? null,
            'start_date' => $startDate,
            'duration_days' => $durationDays,
            'due_date' => $dueDate,
            'status' => $status,
            'progress_percent' => $progressPercent,
            'actual_end_date' => null,
            'sort_order' => ((int) ProjectImplementationSubtask::query()
                ->where('project_implementation_detail_id', $implementationDetail->id)
                ->max('sort_order')) + 1,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $this->syncImplementationDetailFromSubtasks($implementationDetail->fresh('subtasks'), $request->user()?->id);
        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Thêm công việc con');

        return redirect()->back()->with('success', 'Đã thêm công việc con.');
    }

    public function updateImplementationSubtask(Request $request, Project $project, ProjectImplementationDetail $implementationDetail, ProjectImplementationSubtask $subtask): RedirectResponse
    {
        abort_unless($this->canUpdateImplementationSubtask($request->user(), $project), 403);
        $this->ensureSubtaskBelongsToDetail($project, $implementationDetail, $subtask);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'subtask' => 'Không thể cập nhật công việc con khi đầu việc đang khóa.',
            ]);
        }

        $validated = $this->validateImplementationSubtaskPayload($request);
        $assigneeId = $validated['assigned_to'] ?? null;
        if ($assigneeId) {
            $this->ensureEmployeeIsProjectMember($project, (int) $assigneeId);
        }

        $oldProgress = $this->currentProjectProgressPercent($project);
        $startDate = (string) $validated['start_date'];
        $dueDate = (string) $validated['due_date'];
        $this->ensureSubtaskWithinImplementationDetailDeadline($implementationDetail, $startDate, $dueDate);
        $durationDays = $this->calculateDurationDays($startDate, $dueDate);

        $subtask->update([
            'assigned_to' => $assigneeId ? (int) $assigneeId : null,
            'title' => trim((string) $validated['title']),
            'description' => $validated['description'] ?? null,
            'start_date' => $startDate,
            'duration_days' => $durationDays,
            'due_date' => $dueDate,
            'updated_by' => $request->user()?->id,
        ]);

        $this->syncImplementationDetailFromSubtasks($implementationDetail->fresh('subtasks'), $request->user()?->id);
        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Cập nhật công việc con');

        return redirect()->back()->with('success', 'Đã cập nhật công việc con.');
    }

    public function updateImplementationSubtaskStatus(Request $request, Project $project, ProjectImplementationDetail $implementationDetail, ProjectImplementationSubtask $subtask): RedirectResponse
    {
        if (!$this->canUpdateSubtaskStatus($request->user(), $subtask)) {
            abort(403);
        }

        $this->ensureSubtaskBelongsToDetail($project, $implementationDetail, $subtask);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'subtask' => 'Không thể cập nhật trạng thái công việc con khi đầu việc đang khóa.',
            ]);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::DETAIL_STATUSES)],
        ]);

        $oldProgress = $this->currentProjectProgressPercent($project);
        $status = (string) $validated['status'];
        $this->ensureValidImplementationStatusTransition((string) $subtask->status, $status);

        $subtask->update([
            'status' => $status,
            'actual_end_date' => $status === 'completed' ? ($subtask->actual_end_date ?? now()->toDateString()) : null,
            'updated_by' => $request->user()?->id,
        ]);

        $this->syncImplementationDetailFromSubtasks($implementationDetail->fresh('subtasks'), $request->user()?->id);
        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Cập nhật trạng thái công việc con');

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái công việc con.');
    }

    public function storeSubtaskWorkLog(Request $request, Project $project, ProjectImplementationDetail $implementationDetail, ProjectImplementationSubtask $subtask): RedirectResponse
    {
        $this->ensureSubtaskBelongsToDetail($project, $implementationDetail, $subtask);
        abort_unless($this->canLogWorkOnSubtask($request->user(), $subtask), 403);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'work_log' => 'Không thể ghi giờ làm khi dự án hoặc đầu việc đang khóa.',
            ]);
        }

        $validated = $request->validate([
            'work_date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0.25', 'max:24'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'work_date.required' => 'Vui lòng chọn ngày làm việc.',
            'hours.required' => 'Vui lòng nhập số giờ thực tế.',
            'hours.min' => 'Số giờ làm tối thiểu là 0.25 giờ.',
            'hours.max' => 'Số giờ làm tối đa là 24 giờ trong một lần ghi.',
        ]);

        ProjectWorkLog::query()->create([
            'project_id' => $project->id,
            'project_implementation_detail_id' => $implementationDetail->id,
            'project_implementation_subtask_id' => $subtask->id,
            'employee_profile_id' => $subtask->assigned_to ?: $request->user()?->employeeProfile?->id,
            'work_date' => $validated['work_date'],
            'hours' => round((float) $validated['hours'], 2),
            'note' => $validated['note'] ?? null,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $project->update(['updated_by' => $request->user()?->id]);

        return redirect()->back()->with('success', 'Đã ghi nhận giờ làm thực tế cho công việc.');
    }

    public function destroySubtaskWorkLog(
        Request $request,
        Project $project,
        ProjectImplementationDetail $implementationDetail,
        ProjectImplementationSubtask $subtask,
        ProjectWorkLog $workLog
    ): RedirectResponse {
        $this->ensureSubtaskBelongsToDetail($project, $implementationDetail, $subtask);

        if ((int) $workLog->project_implementation_subtask_id !== (int) $subtask->id) {
            abort(404);
        }

        abort_unless($this->canDeleteSubtaskWorkLog($request->user(), $workLog), 403);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'work_log' => 'Không thể xóa log giờ làm khi dự án hoặc đầu việc đang khóa.',
            ]);
        }

        $workLog->delete();

        return redirect()->back()->with('success', 'Đã xóa log giờ làm.');
    }

    public function destroyImplementationSubtask(Request $request, Project $project, ProjectImplementationDetail $implementationDetail, ProjectImplementationSubtask $subtask): RedirectResponse
    {
        abort_unless($this->canDeleteImplementationSubtask($request->user(), $project), 403);
        $this->ensureSubtaskBelongsToDetail($project, $implementationDetail, $subtask);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'subtask' => 'Không thể xóa công việc con khi đầu việc đang khóa.',
            ]);
        }

        $oldProgress = $this->currentProjectProgressPercent($project);
        $subtask->delete();
        $this->syncImplementationDetailFromSubtasks($implementationDetail->fresh('subtasks'), $request->user()?->id);
        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Xóa công việc con');

        return redirect()->back()->with('success', 'Đã xóa công việc con.');
    }

    public function toggleImplementationDetailLock(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canToggleImplementationDetailLock($request->user(), $project), 403);

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'project' => 'Dự án đang bị khóa, không thể thay đổi trạng thái khóa đầu việc.',
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
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Thay đổi khóa đầu việc');

        return redirect()->back()->with('success', $implementationDetail->is_locked
            ? 'Đã khóa đầu việc.'
            : 'Đã mở khóa đầu việc.');
    }

    public function destroyImplementationDetail(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        abort_unless($this->canDeleteImplementationDetail($request->user(), $project), 403);

        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'detail' => 'Đầu việc đang bị khóa, không thể xóa.',
            ]);
        }

        $this->logImplementationChanges($implementationDetail, [
            'deleted' => ['0', '1'],
        ], $request->user()?->id);
        $oldProgress = $this->currentProjectProgressPercent($project);

        $implementationDetail->delete();

        $project->update(['updated_by' => $request->user()?->id]);
        $this->recordImplementationProgressHistory($project, $oldProgress, $request->user()?->id, 'Xóa đầu việc triển khai');

        return redirect()->back()->with('success', 'Đã xóa đầu việc triển khai.');
    }

    public function uploadAttachment(Request $request, Project $project): RedirectResponse
    {
        abort_unless($this->canUploadProjectAttachment($request->user(), $project), 403);

        if ($project->is_locked) {
            return redirect()->back()->withErrors([
                'attachments' => 'Dự án đang bị khóa, không thể tải tệp đính kèm.',
            ]);
        }

        $this->storeAttachments($request, $project);

        $project->update(['updated_by' => $request->user()?->id]);

        return redirect()->back()->with('success', 'Đã tải tệp đính kèm lên dự án.');
    }

    public function uploadImplementationDetailAttachment(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        if ($implementationDetail->project_id !== $project->id) {
            abort(404);
        }

        abort_unless($this->canUploadImplementationAttachment($request->user(), $implementationDetail), 403);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'attachments' => 'Đầu việc đang bị khóa, không thể tải tệp đính kèm.',
            ]);
        }

        $this->storeAttachments($request, $project, $implementationDetail);

        $project->update(['updated_by' => $request->user()?->id]);
        $implementationDetail->update(['updated_by' => $request->user()?->id]);

        return redirect()->back()->with('success', 'Đã tải tệp đính kèm lên đầu việc.');
    }

    public function storeImplementationDetailComment(Request $request, Project $project, ProjectImplementationDetail $implementationDetail): RedirectResponse
    {
        $this->ensureImplementationDetailBelongsToProject($project, $implementationDetail);

        abort_unless($this->canCommentOnImplementationDetail($request->user(), $project, $implementationDetail), 403);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'comment' => 'Dự án hoặc đầu việc đang bị khóa, không thể thêm bình luận.',
            ]);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ], [
            'content.required' => 'Nội dung bình luận là bắt buộc.',
            'content.max' => 'Nội dung bình luận không được vượt quá 2000 ký tự.',
        ]);

        $comment = ProjectDetailComment::query()->create([
            'project_id' => $project->id,
            'implementation_detail_id' => $implementationDetail->id,
            'user_id' => $request->user()?->id,
            'content' => trim((string) $validated['content']),
        ]);

        $this->notifyImplementationDetailCommented(
            $implementationDetail->fresh(['project', 'assignee.user']),
            $comment->fresh(['author:id,name']),
            $request->user()
        );

        return redirect()->back()->with('success', 'Đã gửi bình luận cho đầu việc.');
    }

    public function destroyImplementationDetailComment(
        Request $request,
        Project $project,
        ProjectImplementationDetail $implementationDetail,
        ProjectDetailComment $comment
    ): RedirectResponse {
        $this->ensureImplementationDetailBelongsToProject($project, $implementationDetail);
        $this->ensureImplementationCommentBelongsToDetail($project, $implementationDetail, $comment);

        abort_unless($this->canDeleteImplementationComment($request->user(), $project, $comment), 403);

        if ($project->is_locked || $implementationDetail->is_locked) {
            return redirect()->back()->withErrors([
                'comment' => 'Dự án hoặc đầu việc đang bị khóa, không thể xóa bình luận.',
            ]);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Đã xóa bình luận.');
    }

    public function downloadAttachment(Request $request, Project $project, ProjectAttachment $attachment): StreamedResponse
    {
        $this->ensureAttachmentBelongsToProject($project, $attachment);

        abort_unless($this->canViewProjectAttachment($request->user(), $project), 403);

        if (!Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404);
        }
        $disk = Storage::disk($attachment->disk);

        return $disk->download($attachment->path, $attachment->original_name);
    }

    public function viewAttachment(Request $request, Project $project, ProjectAttachment $attachment): BinaryFileResponse
    {
        $this->ensureAttachmentBelongsToProject($project, $attachment);

        abort_unless($this->canViewProjectAttachment($request->user(), $project), 403);
        abort_unless($this->canPreviewAttachment($attachment), 415);

        $disk = Storage::disk($attachment->disk);
        if (!$disk->exists($attachment->path)) {
            abort(404);
        }

        $filePath = $disk->path($attachment->path);
        $response = response()->file($filePath, [
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
        ]);
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                'inline',
                Str::ascii($attachment->original_name) ?: 'attachment',
                $attachment->original_name
            )
        );

        return $response;
    }

    public function destroyAttachment(Request $request, Project $project, ProjectAttachment $attachment): RedirectResponse
    {
        $this->ensureAttachmentBelongsToProject($project, $attachment);

        abort_unless($this->canDeleteProjectAttachment($request->user(), $project, $attachment), 403);

        if ($project->is_locked || $attachment->implementationDetail?->is_locked) {
            return redirect()->back()->withErrors([
                'attachments' => 'Dự án hoặc đầu việc đang bị khóa, không thể xóa tệp đính kèm.',
            ]);
        }

        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        $project->update(['updated_by' => $request->user()?->id]);

        return redirect()->back()->with('success', 'Đã xóa tệp đính kèm.');
    }

    private function renderProjectsPage(Request $request, string $scope): Response
    {
        $filters = [
            'search' => trim((string) $request->string('search')),
            'status' => (string) $request->string('status'),
            'employee_profile_id' => (int) $request->integer('employee_profile_id'),
            'per_page' => (int) $request->integer('per_page', 10),
        ];
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($filters['per_page'], $allowedPerPage, true)) {
            $filters['per_page'] = 10;
        }

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

        $projectsPaginator = $query
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
                'milestones' => fn ($relation) => $relation
                    ->orderBy('sort_order')
                    ->orderBy('planned_end_date')
                    ->orderBy('id'),
                'attachments' => fn ($relation) => $relation
                    ->whereNull('implementation_detail_id')
                    ->with(['uploader:id,name', 'implementationDetail:id,is_locked'])
                    ->latest('id'),
                'progressHistories' => fn ($relation) => $relation->with('changer:id,name')->latest('changed_at')->limit(30),
                'implementationDetails' => fn ($relation) => $relation
                    ->with([
                        'assignee.user:id,name',
                        'attachments.uploader:id,name',
                        'attachments.implementationDetail:id,is_locked',
                        'comments' => fn ($commentRelation) => $commentRelation
                            ->with('author:id,name')
                            ->orderBy('id'),
                        'subtasks' => fn ($subtaskRelation) => $subtaskRelation
                            ->with([
                                'assignee.user:id,name',
                                'workLogs.employeeProfile.user:id,name',
                                'workLogs.creator:id,name',
                            ])
                            ->orderBy('sort_order')
                            ->orderBy('id'),
                        'logs.updatedBy:id,name',
                    ])
                    ->orderByDesc('id'),
            ])
            ->withCount([
                'members as active_members_count' => fn ($relation) => $relation->where('is_active', true),
            ])
            ->orderByDesc('id')
            ->paginate($filters['per_page'])
            ->withQueryString();

        $projects = $projectsPaginator
            ->getCollection()
            ->map(fn (Project $project) => $this->transformProject($project, $pageUser))
            ->values();

        $canManageProjects = $this->canManageProjects($pageUser);
        $canManageMembers = $this->canManageProjectMembers($pageUser);
        $canManageProjectRoles = $this->canManageProjectRoles($pageUser);
        $canManageImplementationDetails = $this->canManageImplementationDetails($pageUser);
        $canUploadProjectAttachments = $canManageProjects || $canManageMembers;
        $canViewAllProjects = $this->canViewAllProjects($pageUser);

        $employeeOptions = [];
        $employeeProjectOverview = [];

        if ($canManageProjects || $canViewAllProjects) {
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
                    'label' => trim(($profile->employee_code ? ($profile->employee_code . ' - ') : '') . ($profile->user?->name ?? 'Nhân sự')),
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
        }


        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'pagination' => [
                'current_page' => $projectsPaginator->currentPage(),
                'last_page' => $projectsPaginator->lastPage(),
                'per_page' => $projectsPaginator->perPage(),
                'total' => $projectsPaginator->total(),
                'from' => $projectsPaginator->firstItem(),
                'to' => $projectsPaginator->lastItem(),
            ],
            'scope' => $scope,
            'title' => $scope === 'mine' ? 'Dự án của tôi' : 'Danh sách dự án',
            'filters' => $filters,
            'status_options' => collect(self::STATUSES)
                ->map(fn (string $status) => ['value' => $status, 'label' => $this->statusLabel($status)])
                ->values(),
            'employee_options' => $employeeOptions,
            'project_role_options' => $this->projectRoleOptions(),
            'project_role_permission_options' => $this->projectRolePermissionOptions(),
            'employee_project_overview' => $employeeProjectOverview,
            'can_manage_projects' => $canManageProjects,
            'can_view_all_projects' => $canViewAllProjects,
            'can_manage_members' => $canManageMembers,
            'can_manage_project_roles' => $canManageProjectRoles,
            'can_manage_implementation_details' => $canManageImplementationDetails,
            'can_upload_project_attachments' => $canUploadProjectAttachments,
            'can_edit_implementation_schedule' => $this->canEditImplementationSchedule($pageUser),
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
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'description' => ['nullable', 'string'],
            'members' => ['nullable', 'array'],
            'members.*.employee_profile_id' => ['required', 'integer', 'distinct', 'exists:employee_profiles,id'],
            'members.*.role_name' => ['required', 'string', 'max:100'],
            'members.*.joined_at' => ['nullable', 'date'],
        ], [
            'name.required' => 'Tên dự án là bắt buộc.',
            'name.unique' => 'Đã tồn tại dự án cùng tên và ngày bắt đầu.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'members.*.employee_profile_id.required' => 'Vui lòng chọn nhân sự tham gia.',
            'members.*.employee_profile_id.distinct' => 'Nhân sự bị trùng trong danh sách.',
            'members.*.employee_profile_id.exists' => 'Nhân sự không hợp lệ.',
            'members.*.role_name.required' => 'Vai trò trong dự án là bắt buộc.',
        ]);
    }

    private function ensureValidProjectStatusTransition(string $currentStatus, string $nextStatus): void
    {
        if ($currentStatus === $nextStatus) {
            return;
        }

        $allowedNextStatuses = self::STATUS_TRANSITIONS[$currentStatus] ?? [];
        if (in_array($nextStatus, $allowedNextStatuses, true)) {
            return;
        }

        throw ValidationException::withMessages([
            'status' => sprintf(
                'Không thể chuyển trạng thái từ "%s" sang "%s".',
                $this->statusLabel($currentStatus),
                $this->statusLabel($nextStatus)
            ),
        ]);
    }

    private function ensureValidImplementationStatusTransition(string $currentStatus, string $nextStatus): void
    {
        if ($currentStatus === $nextStatus) {
            return;
        }

        $allowedNextStatuses = self::DETAIL_STATUS_TRANSITIONS[$currentStatus] ?? [];
        if (in_array($nextStatus, $allowedNextStatuses, true)) {
            return;
        }

        throw ValidationException::withMessages([
            'status' => sprintf(
                'Không thể chuyển trạng thái từ "%s" sang "%s".',
                $this->implementationStatusLabel($currentStatus),
                $this->implementationStatusLabel($nextStatus)
            ),
            'detail_status' => sprintf(
                'Không thể chuyển trạng thái từ "%s" sang "%s".',
                $this->implementationStatusLabel($currentStatus),
                $this->implementationStatusLabel($nextStatus)
            ),
        ]);
    }

    private function syncMembers(Project $project, array $members, ?User $actor = null): void
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
                $wasActive = (bool) $record->is_active;
                $record->update([
                    'project_role_id' => $projectRole->id,
                    'joined_at' => $member['joined_at'] ?? $record->joined_at ?? $project->start_date,
                    'left_at' => null,
                    'is_active' => true,
                ]);

                if (!$wasActive) {
                    $this->notifyProjectMemberAssigned($project, $record->fresh(['employeeProfile.user', 'role']), $actor);
                }

                continue;
            }

            $createdMember = ProjectMember::query()->create([
                'project_id' => $project->id,
                'employee_profile_id' => $profileId,
                'project_role_id' => $projectRole->id,
                'joined_at' => $member['joined_at'] ?? $project->start_date,
                'is_active' => true,
            ]);

            $this->notifyProjectMemberAssigned($project, $createdMember->fresh(['employeeProfile.user', 'role']), $actor);
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
            'role_name' => 'Vai trò không hợp lệ. Vui lòng chọn vai trò đã được tạo bởi quản trị dự án.',
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
            'note' => $note ?: sprintf('Trạng thái: %s -> %s', $this->statusLabel($oldStatus), $this->statusLabel($newStatus)),
        ]);
    }

    private function notifyProjectMemberAssigned(Project $project, ?ProjectMember $member, ?User $actor = null): void
    {
        $recipientId = $member?->employeeProfile?->user_id;
        if (!$recipientId) {
            return;
        }

        $roleName = $member?->role?->name;
        $message = $roleName
            ? sprintf('Bạn đã được phân công vào dự án "%s" với vai trò "%s".', $project->name, $roleName)
            : sprintf('Bạn đã được phân công vào dự án "%s".', $project->name);

        $this->sendProjectNotification(
            [$recipientId],
            'Bạn được phân công vào dự án',
            $message,
            [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'project_role' => $roleName,
                'action_url' => '/my-projects',
            ],
            '/my-projects',
            $actor?->id,
            Project::class,
            $project->id
        );
    }

    private function notifyProjectMembersAboutStatusChange(Project $project, ?string $oldStatus, string $newStatus, ?User $actor = null): void
    {
        $recipientIds = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('is_active', true)
            ->with('employeeProfile:id,user_id')
            ->get()
            ->pluck('employeeProfile.user_id')
            ->filter()
            ->when($actor, fn ($ids) => $ids->reject(fn ($id) => (int) $id === (int) $actor->id))
            ->unique()
            ->values()
            ->all();

        $this->sendProjectNotification(
            $recipientIds,
            'Trạng thái dự án đã thay đổi',
            sprintf(
                'Dự án "%s" đã đổi trạng thái từ "%s" sang "%s".',
                $project->name,
                $this->statusLabel($oldStatus),
                $this->statusLabel($newStatus)
            ),
            [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'old_status' => $oldStatus,
                'old_status_label' => $this->statusLabel($oldStatus),
                'new_status' => $newStatus,
                'new_status_label' => $this->statusLabel($newStatus),
                'action_url' => '/my-projects',
            ],
            '/my-projects',
            $actor?->id,
            Project::class,
            $project->id
        );
    }

    private function notifyProjectMembersAboutMilestoneChange(ProjectMilestone $milestone, ?User $actor = null, string $action = 'updated'): void
    {
        $project = $milestone->project;
        if (!$project) {
            return;
        }

        $recipientIds = ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('is_active', true)
            ->with('employeeProfile:id,user_id')
            ->get()
            ->pluck('employeeProfile.user_id')
            ->filter()
            ->when($actor, fn ($ids) => $ids->reject(fn ($id) => (int) $id === (int) $actor->id))
            ->unique()
            ->values()
            ->all();

        $title = $action === 'created' ? 'Dự án có mốc quan trọng mới' : 'Mốc quan trọng của dự án đã cập nhật';
        $message = sprintf(
            'Mốc "%s" của dự án "%s" đang ở trạng thái "%s", hạn hoàn thành %s.',
            $milestone->name,
            $project->name,
            $this->implementationStatusLabel($milestone->status),
            optional($milestone->planned_end_date)->format('d/m/Y') ?: '-'
        );

        $this->sendProjectNotification(
            $recipientIds,
            $title,
            $message,
            [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'milestone_id' => $milestone->id,
                'milestone_name' => $milestone->name,
                'status' => $milestone->status,
                'action_url' => '/my-projects',
            ],
            '/my-projects',
            $actor?->id,
            ProjectMilestone::class,
            $milestone->id
        );
    }

    private function notifyImplementationDetailAssigned(?ProjectImplementationDetail $detail, ?User $actor = null): void
    {
        $recipientId = $detail?->assignee?->user_id;
        if (!$detail || !$recipientId) {
            return;
        }

        $projectName = $detail->project?->name ?: 'dự án';

        $this->sendProjectNotification(
            [$recipientId],
            'Bạn được giao đầu việc mới',
            sprintf('Bạn được giao đầu việc "%s" trong dự án "%s".', $detail->content, $projectName),
            [
                'project_id' => $detail->project_id,
                'implementation_detail_id' => $detail->id,
                'project_name' => $projectName,
                'content' => $detail->content,
                'status' => $detail->detail_status,
                'status_label' => $this->implementationStatusLabel($detail->detail_status),
                'progress_percent' => (int) ($detail->progress_percent ?? 0),
                'action_url' => '/my-projects',
            ],
            '/my-projects',
            $actor?->id,
            ProjectImplementationDetail::class,
            $detail->id
        );
    }

    private function notifyImplementationDetailUpdated(?ProjectImplementationDetail $detail, ?User $actor = null, ?string $message = null): void
    {
        if (!$detail) {
            return;
        }

        $projectName = $detail->project?->name ?: 'dự án';
        $recipientIds = $this->implementationDetailStakeholderIds($detail, $actor);

        $this->sendProjectNotification(
            $recipientIds,
            'Đầu việc dự án đã được cập nhật',
            $message ?: sprintf('Đầu việc "%s" trong dự án "%s" đã được cập nhật.', $detail->content, $projectName),
            [
                'project_id' => $detail->project_id,
                'implementation_detail_id' => $detail->id,
                'project_name' => $projectName,
                'content' => $detail->content,
                'status' => $detail->detail_status,
                'status_label' => $this->implementationStatusLabel($detail->detail_status),
                'progress_percent' => (int) ($detail->progress_percent ?? 0),
                'action_url' => '/my-projects',
            ],
            '/my-projects',
            $actor?->id,
            ProjectImplementationDetail::class,
            $detail->id
        );
    }

    private function implementationDetailStakeholderIds(ProjectImplementationDetail $detail, ?User $actor = null): array
    {
        $detail->loadMissing(['project', 'assignee']);

        return collect([
            $detail->assignee?->user_id,
            $detail->project?->created_by,
            $detail->project?->updated_by,
        ])
            ->filter()
            ->when($actor, fn ($ids) => $ids->reject(fn ($id) => (int) $id === (int) $actor->id))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Gửi thông báo dự án an toàn để lỗi broadcast không làm hỏng thao tác chính.
     */
    private function notifyImplementationDetailCommented(
        ?ProjectImplementationDetail $detail,
        ?ProjectDetailComment $comment,
        ?User $actor = null
    ): void {
        if (!$detail || !$comment) {
            return;
        }

        $projectName = $detail->project?->name ?: 'dự án';
        $authorName = $comment->author?->name ?: ($actor?->name ?: 'Một thành viên');

        $this->sendProjectNotification(
            $this->implementationDetailCommentRecipientIds($detail, $actor),
            'Đầu việc có bình luận mới',
            sprintf('%s đã bình luận trong đầu việc "%s".', $authorName, Str::limit($detail->content, 80)),
            [
                'project_id' => $detail->project_id,
                'implementation_detail_id' => $detail->id,
                'project_name' => $projectName,
                'content' => $detail->content,
                'comment_id' => $comment->id,
                'comment_content' => $comment->content,
                'comment_author_name' => $authorName,
                'action_url' => '/my-projects',
            ],
            '/my-projects',
            $actor?->id,
            ProjectDetailComment::class,
            $comment->id
        );
    }

    private function implementationDetailCommentRecipientIds(ProjectImplementationDetail $detail, ?User $actor = null): array
    {
        $commentAuthorIds = ProjectDetailComment::query()
            ->where('implementation_detail_id', $detail->id)
            ->pluck('user_id');

        return $commentAuthorIds
            ->merge($this->implementationDetailStakeholderIds($detail, $actor))
            ->filter()
            ->when($actor, fn ($ids) => $ids->reject(fn ($id) => (int) $id === (int) $actor->id))
            ->unique()
            ->values()
            ->all();
    }

    private function sendProjectNotification(
        array $recipientIds,
        string $title,
        string $message,
        array $data,
        string $url,
        ?int $actorId,
        string $referenceType,
        int $referenceId,
    ): void {
        $recipientIds = collect($recipientIds)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($recipientIds)) {
            return;
        }

        try {
            $this->notificationService->createForUsers(
                $recipientIds,
                $title,
                $message,
                $data,
                $url,
                $this->currentNotificationSubdomain(),
                'project',
                $actorId,
                $referenceType,
                $referenceId
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function currentNotificationSubdomain(): string
    {
        $host = request()->getHost();
        $parts = explode('.', $host);

        return count($parts) >= 3 ? $parts[0] : 'main';
    }

    private function transformProject(Project $project, $pageUser): array
    {
        $visibleImplementationDetails = $this->visibleImplementationDetails($project, $pageUser);
        $progressSummary = $this->buildProjectProgressSummary($project->implementationDetails);
        $workReport = $this->buildProjectWorkReport($visibleImplementationDetails, $pageUser);

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
            'work_report' => $workReport,
            'is_delayed' => $progressSummary['delayed_tasks'] > 0,
            'delay_warning' => $progressSummary['delayed_tasks'] > 0
                ? sprintf('Dự án có %d đầu việc chậm tiến độ.', $progressSummary['delayed_tasks'])
                : null,
            'start_date' => optional($project->start_date)->format('Y-m-d'),
            'end_date' => optional($project->end_date)->format('Y-m-d'),
            'description' => $project->description,
            'is_locked' => (bool) $project->is_locked,
            'can_manage_members' => $this->canManageProjectMembers($pageUser, $project),
            'can_add_member' => $this->canAddProjectMember($pageUser, $project),
            'can_update_member_role' => $this->canUpdateProjectMemberRole($pageUser, $project),
            'can_remove_member' => $this->canRemoveProjectMember($pageUser, $project),
            'can_manage_project_roles' => $this->canManageProjectRoles($pageUser),
            'can_manage_implementation_details' => $this->canManageImplementationDetails($pageUser, $project),
            'can_create_implementation_detail' => $this->canCreateImplementationDetail($pageUser, $project),
            'can_update_implementation_detail' => $this->canUpdateImplementationDetail($pageUser, $project),
            'can_delete_implementation_detail' => $this->canDeleteImplementationDetail($pageUser, $project),
            'can_toggle_implementation_detail_lock' => $this->canToggleImplementationDetailLock($pageUser, $project),
            'can_create_implementation_subtask' => $this->canCreateImplementationSubtask($pageUser, $project),
            'can_update_implementation_subtask' => $this->canUpdateImplementationSubtask($pageUser, $project),
            'can_delete_implementation_subtask' => $this->canDeleteImplementationSubtask($pageUser, $project),
            'can_update_implementation_subtask_status' => $this->canUpdateImplementationSubtaskStatus($pageUser, $project),
            'can_upload_project_attachments' => $this->canUploadProjectAttachment($pageUser, $project),
            'can_edit_implementation_schedule' => $this->canEditImplementationSchedule($pageUser, $project),
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
                'permissions' => $this->normalizeProjectRolePermissions($role->permissions ?? []),
            ])->values(),
            'milestones' => $project->milestones->map(fn (ProjectMilestone $milestone) => [
                'id' => $milestone->id,
                'phase_name' => $milestone->phase_name,
                'name' => $milestone->name,
                'description' => $milestone->description,
                'planned_start_date' => optional($milestone->planned_start_date)->format('Y-m-d'),
                'planned_end_date' => optional($milestone->planned_end_date)->format('Y-m-d'),
                'completed_at' => optional($milestone->completed_at)->format('Y-m-d'),
                'status' => $milestone->status,
                'status_label' => $this->implementationStatusLabel($milestone->status),
                'is_due_soon' => $this->isMilestoneDueSoon($milestone),
                'is_overdue' => $this->isMilestoneOverdue($milestone),
                'can_update' => $this->canUpdateImplementationDetail($pageUser, $project) && !$project->is_locked,
                'can_delete' => $this->canDeleteImplementationDetail($pageUser, $project) && !$project->is_locked,
            ])->values(),
            'role_options' => $this->projectRoleOptions($project),
            'attachments' => $project->attachments
                ->map(fn (ProjectAttachment $attachment) => $this->transformAttachment($attachment, $project, $pageUser))
                ->values(),
            'status_histories' => $project->progressHistories->map(fn (ProjectProgressHistory $history) => [
                'id' => $history->id,
                'old_progress' => (int) $history->old_progress,
                'new_progress' => (int) $history->new_progress,
                'changed_at' => optional($history->changed_at)->format('Y-m-d H:i:s'),
                'changed_by_name' => $history->changer?->name,
                'note' => $history->note,
            ])->values(),
            'implementation_details' => $visibleImplementationDetails->map(function (ProjectImplementationDetail $detail) use ($pageUser, $project) {
                $canUpdateStatus = $this->canUpdateImplementationStatus($pageUser, $detail);
                $visibleSubtasks = $this->visibleSubtasksForDetail($detail, $pageUser);

                return [
                    'id' => $detail->id,
                    'content' => $detail->content,
                    'assigned_to' => $detail->assigned_to,
                    'assigned_name' => $detail->assignee?->user?->name,
                    'assigned_code' => $detail->assignee?->employee_code,
                    'execution_date' => optional($detail->execution_date)->format('Y-m-d'),
                    'duration_days' => (int) $detail->duration_days,
                    'expected_end_date' => $this->expectedEndDateForDetail($detail),
                    'actual_end_date' => optional($detail->actual_end_date)->format('Y-m-d'),
                    'detail_status' => $detail->detail_status,
                    'detail_status_label' => $this->implementationStatusLabel($detail->detail_status),
                    'progress_percent' => $this->normalizedDetailProgress($detail),
                    'is_locked' => (bool) $detail->is_locked,
                    'is_delayed' => $this->isImplementationDetailDelayed($detail),
                    'delay_days' => $this->implementationDetailDelayDays($detail),
                    'can_update_status' => $canUpdateStatus && !$detail->is_locked,
                    'can_update_detail' => $this->canUpdateImplementationDetail($pageUser, $project)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'can_delete_detail' => $this->canDeleteImplementationDetail($pageUser, $project)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'can_create_subtask' => $this->canCreateImplementationSubtask($pageUser, $project)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'can_update_subtask' => $this->canUpdateImplementationSubtask($pageUser, $project)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'can_delete_subtask' => $this->canDeleteImplementationSubtask($pageUser, $project)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'can_toggle_lock' => $this->canToggleImplementationDetailLock($pageUser, $project)
                        && !$project->is_locked,
                    'can_comment' => $this->canCommentOnImplementationDetail($pageUser, $project, $detail)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'can_upload_attachment' => $this->canUploadImplementationAttachment($pageUser, $detail)
                        && !$project->is_locked
                        && !$detail->is_locked,
                    'subtasks' => $visibleSubtasks
                        ->map(fn (ProjectImplementationSubtask $subtask) => [
                            'id' => $subtask->id,
                            'title' => $subtask->title,
                            'description' => $subtask->description,
                            'assigned_to' => $subtask->assigned_to,
                            'assigned_name' => $subtask->assignee?->user?->name,
                            'assigned_code' => $subtask->assignee?->employee_code,
                            'start_date' => optional($subtask->start_date)->format('Y-m-d'),
                            'duration_days' => (int) $subtask->duration_days,
                            'due_date' => optional($subtask->due_date)->format('Y-m-d'),
                            'actual_end_date' => optional($subtask->actual_end_date)->format('Y-m-d'),
                            'status' => $subtask->status,
                            'status_label' => $this->implementationStatusLabel($subtask->status),
                            'weight_percent' => (int) $subtask->weight_percent,
                            'estimated_hours' => $this->estimatedHoursForSubtask($subtask),
                            'actual_hours' => $this->actualHoursForSubtask($subtask),
                            'variance_hours' => round($this->actualHoursForSubtask($subtask) - $this->estimatedHoursForSubtask($subtask), 2),
                            'can_update_status' => $this->canUpdateSubtaskStatus($pageUser, $subtask)
                                && !$project->is_locked
                                && !$detail->is_locked,
                            'can_log_work' => $this->canLogWorkOnSubtask($pageUser, $subtask)
                                && !$project->is_locked
                                && !$detail->is_locked,
                            'work_logs' => $subtask->workLogs
                                ->sortByDesc('work_date')
                                ->take(5)
                                ->values()
                                ->map(fn (ProjectWorkLog $workLog) => [
                                    'id' => $workLog->id,
                                    'work_date' => optional($workLog->work_date)->format('Y-m-d'),
                                    'hours' => (float) $workLog->hours,
                                    'note' => $workLog->note,
                                    'employee_name' => $workLog->employeeProfile?->user?->name,
                                    'created_by_name' => $workLog->creator?->name,
                                    'can_delete' => $this->canDeleteSubtaskWorkLog($pageUser, $workLog)
                                        && !$project->is_locked
                                        && !$detail->is_locked,
                                ]),
                            'is_delayed' => $this->isSubtaskDelayed($subtask),
                            'delay_days' => $this->isSubtaskDelayed($subtask)
                                ? (int) $subtask->due_date->copy()->startOfDay()->diffInDays(now()->startOfDay())
                                : 0,
                        ])
                        ->values(),
                    'subtask_summary' => [
                        'total' => $visibleSubtasks->filter(fn (ProjectImplementationSubtask $subtask) => $subtask->status !== 'cancelled')->count(),
                        'completed' => $visibleSubtasks->filter(fn (ProjectImplementationSubtask $subtask) => $subtask->status === 'completed')->count(),
                        'progress_percent' => $visibleSubtasks->isNotEmpty() ? $this->subtaskCollectionProgress($visibleSubtasks) : null,
                    ],
                    'attachments' => $detail->attachments
                        ->map(fn (ProjectAttachment $attachment) => $this->transformAttachment($attachment, $project, $pageUser))
                        ->values(),
                    'comments' => $detail->comments
                        ->map(fn (ProjectDetailComment $comment) => [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'author_id' => $comment->user_id,
                            'author_name' => $comment->author?->name,
                            'created_at' => optional($comment->created_at)->format('Y-m-d H:i:s'),
                            'can_delete' => $this->canDeleteImplementationComment($pageUser, $project, $comment)
                                && !$project->is_locked
                                && !$detail->is_locked,
                        ])
                        ->values(),
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

    private function storeAttachments(Request $request, Project $project, ?ProjectImplementationDetail $implementationDetail = null): void
    {
        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,ppt,pptx,zip,rar'],
        ], [
            'files.required' => 'Vui lòng chọn ít nhất một tệp đính kèm.',
            'files.array' => 'Dữ liệu tệp đính kèm không hợp lệ.',
            'files.max' => 'Chỉ được tải tối đa 10 tệp mỗi lần.',
            'files.*.required' => 'Tệp đính kèm là bắt buộc.',
            'files.*.file' => 'Tệp đính kèm không hợp lệ.',
            'files.*.max' => 'Mỗi tệp đính kèm không được vượt quá 10 MB.',
            'files.*.mimes' => 'Tệp đính kèm chỉ hỗ trợ hình ảnh, PDF, Word, Excel, PowerPoint, TXT, CSV, ZIP hoặc RAR.',
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $project, $implementationDetail, $validated, &$storedPaths): void {
                foreach ($validated['files'] as $file) {
                    $path = $file->store(
                        sprintf('project-attachments/%d%s', $project->id, $implementationDetail ? ('/details/' . $implementationDetail->id) : ''),
                        'local'
                    );
                    $storedPaths[] = $path;

                    ProjectAttachment::query()->create([
                        'project_id' => $project->id,
                        'implementation_detail_id' => $implementationDetail?->id,
                        'uploaded_by' => $request->user()?->id,
                        'disk' => 'local',
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'size' => (int) $file->getSize(),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);

            throw $exception;
        }
    }

    private function transformAttachment(ProjectAttachment $attachment, Project $project, $pageUser): array
    {
        return [
            'id' => $attachment->id,
            'original_name' => $attachment->original_name,
            'mime_type' => $attachment->mime_type,
            'size' => (int) $attachment->size,
            'size_label' => $this->formatFileSize((int) $attachment->size),
            'uploaded_by_name' => $attachment->uploader?->name,
            'created_at' => optional($attachment->created_at)->format('Y-m-d H:i:s'),
            'preview_url' => route('projects.attachments.view', [$project, $attachment]),
            'download_url' => route('projects.attachments.download', [$project, $attachment]),
            'can_preview' => $this->canPreviewAttachment($attachment),
            'can_delete' => $this->canDeleteProjectAttachment($pageUser, $project, $attachment)
                && !$project->is_locked
                && !$attachment->implementationDetail?->is_locked,
        ];
    }

    private function canPreviewAttachment(ProjectAttachment $attachment): bool
    {
        $mimeType = strtolower((string) $attachment->mime_type);
        $extension = strtolower(pathinfo((string) $attachment->original_name, PATHINFO_EXTENSION));

        return str_starts_with($mimeType, 'image/')
            || in_array($mimeType, ['application/pdf', 'text/plain', 'text/csv'], true)
            || in_array($extension, ['pdf', 'txt', 'csv'], true);
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / 1024 / 1024, 1) . ' MB';
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
        if (
            $this->canManageImplementationDetails($user, $project)
            || $this->canManageImplementationSubtasks($user, $project)
            || $this->canManageProjects($user)
            || $this->canViewAllProjects($user)
        ) {
            return $project->implementationDetails;
        }

        if (!$this->isActiveProjectMember($user, $project)) {
            return collect();
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return collect();
        }

        return $project->implementationDetails
            ->filter(function (ProjectImplementationDetail $detail) use ($profileId): bool {
                if ((int) $detail->assigned_to === (int) $profileId) {
                    return true;
                }

                return $detail->subtasks
                    ->contains(fn (ProjectImplementationSubtask $subtask) => (int) $subtask->assigned_to === (int) $profileId);
            })
            ->values();
    }

    private function visibleSubtasksForDetail(ProjectImplementationDetail $detail, $user): Collection
    {
        $project = $detail->project;
        if (
            $this->canManageImplementationDetails($user, $project)
            || $this->canManageImplementationSubtasks($user, $project)
            || $this->canManageProjects($user)
            || $this->canViewAllProjects($user)
        ) {
            return $detail->subtasks->values();
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return collect();
        }

        return $detail->subtasks
            ->filter(fn (ProjectImplementationSubtask $subtask) => (int) $subtask->assigned_to === (int) $profileId)
            ->values();
    }

    private function canUpdateSubtaskStatus($user, ProjectImplementationSubtask $subtask): bool
    {
        $detail = $subtask->implementationDetail;
        $project = $detail?->project;

        if ($this->canUpdateImplementationSubtaskStatus($user, $project)) {
            return true;
        }

        if (!$user?->hasPositionCapability(PositionCapability::UPDATE_PROJECT_TASK_STATUS)) {
            return false;
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return (int) $subtask->assigned_to === (int) $profileId;
    }

    private function canLogWorkOnSubtask($user, ProjectImplementationSubtask $subtask): bool
    {
        $detail = $subtask->implementationDetail;
        $project = $detail?->project;

        if (
            $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_subtask_status')
            || $this->canManageProjects($user)
        ) {
            return true;
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId || !$project || !$this->isActiveProjectMember($user, $project)) {
            return false;
        }

        return (int) $subtask->assigned_to === (int) $profileId;
    }

    private function canDeleteSubtaskWorkLog($user, ProjectWorkLog $workLog): bool
    {
        $subtask = $workLog->subtask;
        $detail = $subtask?->implementationDetail;
        $project = $detail?->project;

        if (
            $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_subtask_status')
            || $this->canManageProjects($user)
        ) {
            return true;
        }

        return (int) $workLog->created_by === (int) $user?->id;
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
                'assigned_to' => 'Nhân sự được giao phải là thành viên đang hoạt động của dự án.',
            ]);
        }
    }

    private function validateMilestonePayload(Request $request): array
    {
        return $request->validate([
            'phase_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'planned_start_date' => ['nullable', 'date'],
            'planned_end_date' => ['required', 'date', 'after_or_equal:planned_start_date'],
            'status' => ['nullable', Rule::in(self::DETAIL_STATUSES)],
        ], [
            'phase_name.required' => 'Vui lòng nhập giai đoạn của mốc.',
            'name.required' => 'Vui lòng nhập tên mốc quan trọng.',
            'planned_end_date.required' => 'Vui lòng chọn hạn hoàn thành mốc.',
            'planned_end_date.after_or_equal' => 'Hạn hoàn thành mốc phải sau hoặc bằng ngày bắt đầu.',
        ]);
    }

    private function validateImplementationSubtaskPayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'title.required' => 'Vui lòng nhập tên công việc con.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu công việc con.',
            'duration_days.required' => 'Vui lòng nhập số ngày thực hiện công việc con.',
        ]);
    }

    private function calculateExpectedEndDate(string $executionDate, int $durationDays): string
    {
        return Carbon::parse($executionDate)
            ->addDays(max(1, $durationDays) - 1)
            ->toDateString();
    }

    private function calculateDurationDays(string $startDate, string $dueDate): int
    {
        return max(
            1,
            Carbon::parse($startDate)->startOfDay()->diffInDays(Carbon::parse($dueDate)->startOfDay()) + 1
        );
    }

    private function ensureImplementationDetailWithinProjectDeadline(Project $project, string $executionDate, string $expectedEndDate): void
    {
        if ($project->start_date && Carbon::parse($executionDate)->startOfDay()->lt($project->start_date->copy()->startOfDay())) {
            throw ValidationException::withMessages([
                'execution_date' => 'Ngày bắt đầu đầu việc chính không được trước ngày bắt đầu dự án.',
            ]);
        }

        if ($project->end_date && Carbon::parse($expectedEndDate)->startOfDay()->gt($project->end_date->copy()->startOfDay())) {
            throw ValidationException::withMessages([
                'duration_days' => sprintf(
                    'Đầu việc chính không được vượt quá thời hạn dự án (%s).',
                    $project->end_date->format('d/m/Y')
                ),
            ]);
        }
    }

    private function ensureSubtaskWithinImplementationDetailDeadline(ProjectImplementationDetail $detail, string $startDate, string $dueDate): void
    {
        $detailStartDate = $detail->execution_date?->toDateString();
        $detailEndDate = $this->expectedEndDateForDetail($detail);

        if ($detailStartDate && Carbon::parse($startDate)->startOfDay()->lt(Carbon::parse($detailStartDate)->startOfDay())) {
            throw ValidationException::withMessages([
                'start_date' => 'Ngày bắt đầu công việc con không được trước ngày bắt đầu đầu việc chính.',
            ]);
        }

        if ($detailEndDate && Carbon::parse($dueDate)->startOfDay()->gt(Carbon::parse($detailEndDate)->startOfDay())) {
            throw ValidationException::withMessages([
                'due_date' => sprintf(
                    'Công việc con không được vượt quá thời hạn đầu việc chính (%s).',
                    Carbon::parse($detailEndDate)->format('d/m/Y')
                ),
            ]);
        }
    }

    private function ensureExistingSubtasksWithinDetailDeadline(ProjectImplementationDetail $detail, string $executionDate, string $expectedEndDate): void
    {
        $invalidSubtask = $detail->subtasks()
            ->where(function (Builder $query) use ($executionDate, $expectedEndDate): void {
                $query
                    ->whereDate('start_date', '<', $executionDate)
                    ->orWhereDate('due_date', '>', $expectedEndDate);
            })
            ->first();

        if ($invalidSubtask) {
            throw ValidationException::withMessages([
                'detail' => sprintf(
                    'Không thể đổi thời hạn đầu việc vì công việc con "%s" đang nằm ngoài khoảng %s - %s.',
                    $invalidSubtask->title,
                    Carbon::parse($executionDate)->format('d/m/Y'),
                    Carbon::parse($expectedEndDate)->format('d/m/Y')
                ),
            ]);
        }
    }

    private function estimatedHoursForSubtask(ProjectImplementationSubtask $subtask): float
    {
        return (float) (max(1, (int) $subtask->duration_days) * 8);
    }

    private function actualHoursForSubtask(ProjectImplementationSubtask $subtask): float
    {
        if (!$subtask->relationLoaded('workLogs')) {
            return (float) ProjectWorkLog::query()
                ->where('project_implementation_subtask_id', $subtask->id)
                ->sum('hours');
        }

        return round((float) $subtask->workLogs->sum(fn (ProjectWorkLog $workLog) => (float) $workLog->hours), 2);
    }

    private function buildProjectWorkReport(Collection $details, $pageUser): array
    {
        $subtasks = $details
            ->flatMap(fn (ProjectImplementationDetail $detail) => $this->visibleSubtasksForDetail($detail, $pageUser))
            ->filter(fn (ProjectImplementationSubtask $subtask) => $subtask->status !== 'cancelled')
            ->values();

        $estimatedHours = round((float) $subtasks->sum(fn (ProjectImplementationSubtask $subtask) => $this->estimatedHoursForSubtask($subtask)), 2);
        $actualHours = round((float) $subtasks->sum(fn (ProjectImplementationSubtask $subtask) => $this->actualHoursForSubtask($subtask)), 2);

        $employees = $subtasks
            ->flatMap(fn (ProjectImplementationSubtask $subtask) => $subtask->workLogs ?? collect())
            ->groupBy('employee_profile_id')
            ->map(function (Collection $logs) {
                $first = $logs->first();
                $hours = round((float) $logs->sum(fn (ProjectWorkLog $workLog) => (float) $workLog->hours), 2);

                return [
                    'employee_profile_id' => $first?->employee_profile_id,
                    'employee_name' => $first?->employeeProfile?->user?->name ?? 'Nhân sự',
                    'employee_code' => $first?->employeeProfile?->employee_code,
                    'actual_hours' => $hours,
                    'log_count' => $logs->count(),
                ];
            })
            ->sortByDesc('actual_hours')
            ->values();

        return [
            'estimated_hours' => $estimatedHours,
            'actual_hours' => $actualHours,
            'variance_hours' => round($actualHours - $estimatedHours, 2),
            'efficiency_percent' => $estimatedHours > 0 ? round(($actualHours / $estimatedHours) * 100, 1) : 0,
            'employee_hours' => $employees,
        ];
    }

    private function expectedEndDateForDetail(ProjectImplementationDetail $detail): ?string
    {
        if ($detail->relationLoaded('subtasks') && $detail->subtasks->isNotEmpty()) {
            $maxDueDate = $detail->subtasks->max('due_date');

            return $maxDueDate ? Carbon::parse($maxDueDate)->toDateString() : null;
        }

        if (!$detail->execution_date) {
            return optional($detail->expected_end_date)->format('Y-m-d');
        }

        return $this->calculateExpectedEndDate(
            $detail->execution_date->toDateString(),
            max(1, (int) $detail->duration_days)
        );
    }

    private function normalizeProgressByStatus(int $progressPercent, string $status): int
    {
        $progress = max(0, min(100, $progressPercent));

        if ($status === 'planned' || $status === 'cancelled') {
            return 0;
        }

        if ($status === 'completed') {
            return 100;
        }

        if ($status === 'in_progress') {
            return max(1, min(99, $progress));
        }

        return $progress;
    }

    private function normalizedDetailProgress(ProjectImplementationDetail $detail): int
    {
        if ($detail->relationLoaded('subtasks') && $detail->subtasks->isNotEmpty()) {
            return $this->subtaskCollectionProgress($detail->subtasks);
        }

        return $this->normalizeProgressByStatus(
            (int) ($detail->progress_percent ?? 0),
            (string) $detail->detail_status
        );
    }

    private function subtaskCollectionProgress(Collection $subtasks): int
    {
        $activeSubtasks = $subtasks
            ->filter(fn (ProjectImplementationSubtask $subtask) => $subtask->status !== 'cancelled')
            ->values();

        $totalCount = $activeSubtasks->count();

        if ($totalCount === 0) {
            return 0;
        }

        $completedCount = $activeSubtasks->where('status', 'completed')->count();

        return (int) round(($completedCount / $totalCount) * 100);
    }

    private function syncImplementationDetailFromSubtasks(ProjectImplementationDetail $detail, ?int $userId): void
    {
        $subtasks = $detail->subtasks;
        if ($subtasks->isEmpty()) {
            return;
        }

        $activeSubtasks = $subtasks
            ->filter(fn (ProjectImplementationSubtask $subtask) => $subtask->status !== 'cancelled')
            ->values();

        $progress = $this->subtaskCollectionProgress($subtasks);
        $status = 'planned';
        if ($activeSubtasks->isNotEmpty() && $activeSubtasks->every(fn (ProjectImplementationSubtask $subtask) => $subtask->status === 'completed')) {
            $status = 'completed';
        } elseif ($progress > 0 || $activeSubtasks->contains(fn (ProjectImplementationSubtask $subtask) => $subtask->status === 'in_progress')) {
            $status = 'in_progress';
        }

        $minStartDate = $subtasks->min('start_date');
        $maxDueDate = $subtasks->max('due_date');
        $startDate = $minStartDate ? Carbon::parse($minStartDate)->toDateString() : null;
        $dueDate = $maxDueDate ? Carbon::parse($maxDueDate)->toDateString() : null;
        $totalDuration = max(1, (int) $activeSubtasks->sum(fn (ProjectImplementationSubtask $subtask) => max(1, (int) $subtask->duration_days)));

        $detail->update([
            'execution_date' => $startDate ?? $detail->execution_date,
            'duration_days' => $totalDuration,
            'expected_end_date' => $dueDate ?? $detail->expected_end_date,
            'detail_status' => $status,
            'progress_percent' => $progress,
            'actual_end_date' => $status === 'completed' ? ($detail->actual_end_date ?? now()->toDateString()) : null,
            'updated_by' => $userId,
        ]);
    }

    private function isImplementationDetailDelayed(ProjectImplementationDetail $detail): bool
    {
        if ($detail->relationLoaded('subtasks') && $detail->subtasks->isNotEmpty()) {
            return $detail->subtasks->contains(fn (ProjectImplementationSubtask $subtask) => $this->isSubtaskDelayed($subtask));
        }

        if ($detail->detail_status === 'completed' || $detail->detail_status === 'cancelled') {
            return false;
        }

        $expectedEndDate = $this->expectedEndDateForDetail($detail);
        if (!$expectedEndDate) {
            return false;
        }

        return Carbon::parse($expectedEndDate)->startOfDay()->lt(now()->startOfDay());
    }

    private function isSubtaskDelayed(ProjectImplementationSubtask $subtask): bool
    {
        if ($subtask->status === 'completed' || $subtask->status === 'cancelled') {
            return false;
        }

        return $subtask->due_date
            && $subtask->due_date->copy()->startOfDay()->lt(now()->startOfDay());
    }

    private function isMilestoneDueSoon(ProjectMilestone $milestone): bool
    {
        if ($milestone->status === 'completed' || $milestone->status === 'cancelled' || !$milestone->planned_end_date) {
            return false;
        }

        $today = now()->startOfDay();
        $endDate = $milestone->planned_end_date->copy()->startOfDay();

        return $endDate->gte($today) && $endDate->diffInDays($today) <= 3;
    }

    private function isMilestoneOverdue(ProjectMilestone $milestone): bool
    {
        if ($milestone->status === 'completed' || $milestone->status === 'cancelled' || !$milestone->planned_end_date) {
            return false;
        }

        return $milestone->planned_end_date->copy()->startOfDay()->lt(now()->startOfDay());
    }

    private function implementationDetailDelayDays(ProjectImplementationDetail $detail): int
    {
        if ($detail->relationLoaded('subtasks') && $detail->subtasks->isNotEmpty()) {
            return (int) $detail->subtasks
                ->filter(fn (ProjectImplementationSubtask $subtask) => $this->isSubtaskDelayed($subtask))
                ->map(fn (ProjectImplementationSubtask $subtask) => $subtask->due_date->copy()->startOfDay()->diffInDays(now()->startOfDay()))
                ->max();
        }

        if (!$this->isImplementationDetailDelayed($detail)) {
            return 0;
        }

        return (int) Carbon::parse($this->expectedEndDateForDetail($detail))
            ->startOfDay()
            ->diffInDays(now()->startOfDay());
    }

    private function currentProjectProgressPercent(Project $project): int
    {
        $details = ProjectImplementationDetail::query()
            ->where('project_id', $project->id)
            ->with('subtasks')
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
            'note' => sprintf('%s: %d%% -> %d%% (theo trọng số số ngày đầu việc)', $action, $oldProgress, $newProgress),
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
        $delayedTasks = (int) $activeDetails
            ->filter(fn (ProjectImplementationDetail $detail) => $this->isImplementationDetailDelayed($detail))
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
        $weightedProgress = (float) $activeDetails
            ->sum(fn (ProjectImplementationDetail $detail) => max(1, (int) $detail->duration_days) * $this->normalizedDetailProgress($detail));
        $progressPercent = $totalDuration > 0
            ? (int) round($weightedProgress / $totalDuration)
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
            'planned' => 'Chưa làm',
            'in_progress' => 'Đang làm',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Tạm dừng',
            default => '-',
        };
    }

    private function detailLogFieldLabel(?string $field): string
    {
        return match ($field) {
            'content' => 'Nội dung',
            'assigned_to' => 'Nhân sự',
            'execution_date' => 'Ngày thực hiện',
            'duration_days' => 'Số ngày',
            'expected_end_date' => 'Ngày hoàn thành dự kiến',
            'detail_status' => 'Trạng thái',
            'progress_percent' => 'Tiến độ',
            'is_locked' => 'Khóa đầu việc',
            'deleted' => 'Xóa đầu việc',
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
            'planning' => 'Kế hoạch',
            'in_progress' => 'Đang triển khai',
            'on_hold' => 'Tạm dừng',
            'completed' => 'Hoàn thành',
            default => '-',
        };
    }

    private function canViewAllProjects($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasPositionCapability(PositionCapability::VIEW_ALL_PROJECTS)) {
            return true;
        }

        return false;
    }

    private function canManageProjects($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasPositionCapability(PositionCapability::MANAGE_PROJECTS);
    }

    private function projectRolePermissionOptions(): array
    {
        return [
            [
                'key' => 'members',
                'label' => 'Nhân sự dự án',
                'permissions' => [
                    ['value' => 'add_project_member', 'label' => 'Thêm nhân sự', 'description' => 'Đưa nhân sự mới vào dự án.'],
                    ['value' => 'remove_project_member', 'label' => 'Loại nhân sự', 'description' => 'Cho thành viên rời khỏi dự án.'],
                ],
            ],
            [
                'key' => 'implementation',
                'label' => 'Đầu việc triển khai',
                'permissions' => [
                    ['value' => 'create_implementation_detail', 'label' => 'Tạo đầu việc', 'description' => 'Thêm đầu việc triển khai mới.'],
                    ['value' => 'update_implementation_detail', 'label' => 'Sửa đầu việc', 'description' => 'Sửa nội dung và phân công đầu việc.'],
                    ['value' => 'delete_implementation_detail', 'label' => 'Xóa đầu việc', 'description' => 'Xóa đầu việc chưa bị khóa.'],
                    ['value' => 'toggle_implementation_detail_lock', 'label' => 'Khóa/mở khóa đầu việc', 'description' => 'Chặn hoặc mở lại chỉnh sửa đầu việc.'],
                    ['value' => 'edit_implementation_schedule', 'label' => 'Điều chỉnh lịch', 'description' => 'Sửa ngày thực hiện và số ngày thực hiện.'],
                    ['value' => 'update_implementation_status', 'label' => 'Cập nhật tiến độ', 'description' => 'Cập nhật trạng thái và phần trăm tiến độ.'],
                ],
            ],
            [
                'key' => 'implementation_subtasks',
                'label' => 'Công việc con',
                'permissions' => [
                    ['value' => 'create_implementation_subtask', 'label' => 'Tạo công việc con', 'description' => 'Thêm công việc con vào đầu việc chính.'],
                    ['value' => 'update_implementation_subtask', 'label' => 'Sửa công việc con', 'description' => 'Sửa tên, người thực hiện và thời hạn công việc con.'],
                    ['value' => 'delete_implementation_subtask', 'label' => 'Xóa công việc con', 'description' => 'Xóa công việc con trong đầu việc chính.'],
                    ['value' => 'update_implementation_subtask_status', 'label' => 'Cập nhật trạng thái công việc con', 'description' => 'Cập nhật trạng thái công việc con trong dự án.'],
                ],
            ],
            [
                'key' => 'attachments',
                'label' => 'Tệp đính kèm',
                'permissions' => [
                    ['value' => 'upload_project_attachment', 'label' => 'Tải tệp dự án', 'description' => 'Tải tệp chung lên dự án.'],
                    ['value' => 'upload_implementation_attachment', 'label' => 'Tải tệp đầu việc', 'description' => 'Tải tệp vào từng đầu việc.'],
                    ['value' => 'delete_project_attachment', 'label' => 'Xóa tệp', 'description' => 'Xóa tệp đính kèm của dự án hoặc đầu việc.'],
                ],
            ],
        ];

        return [
            [
                'value' => 'manage_members',
                'label' => 'Quản lý thành viên',
                'description' => 'Thêm, đổi vai trò hoặc loại nhân sự khỏi riêng dự án này.',
            ],
            [
                'value' => 'manage_implementation_details',
                'label' => 'Quản lý đầu việc',
                'description' => 'Tạo, sửa, khóa hoặc xóa đầu việc triển khai trong dự án.',
            ],
            [
                'value' => 'update_implementation_status',
                'label' => 'Cập nhật tiến độ',
                'description' => 'Cập nhật trạng thái và phần trăm tiến độ của các đầu việc trong dự án.',
            ],
            [
                'value' => 'manage_attachments',
                'label' => 'Quản lý tệp dự án',
                'description' => 'Tải lên hoặc xóa tệp đính kèm của dự án.',
            ],
        ];
    }

    private function normalizeProjectRolePermissions(array $permissions): array
    {
        return collect($permissions)
            ->flatMap(function ($permission) {
                if (!is_string($permission)) {
                    return [];
                }

                return self::LEGACY_PROJECT_ROLE_PERMISSION_ALIASES[$permission] ?? [$permission];
            })
            ->filter(fn ($permission) => in_array($permission, self::PROJECT_ROLE_PERMISSIONS, true))
            ->unique()
            ->values()
            ->all();

        return collect($permissions)
            ->filter(fn ($permission) => is_string($permission) && in_array($permission, self::PROJECT_ROLE_PERMISSIONS, true))
            ->unique()
            ->values()
            ->all();
    }

    private function validProjectRolePermissions(): array
    {
        return array_values(array_unique([
            ...self::PROJECT_ROLE_PERMISSIONS,
            ...array_keys(self::LEGACY_PROJECT_ROLE_PERMISSION_ALIASES),
        ]));
    }

    private function projectRolePermissionLookupValues(string $permission): array
    {
        $values = [$permission];

        foreach (self::LEGACY_PROJECT_ROLE_PERMISSION_ALIASES as $legacyPermission => $expandedPermissions) {
            if (in_array($permission, $expandedPermissions, true)) {
                $values[] = $legacyPermission;
            }
        }

        return array_values(array_unique($values));
    }

    private function userHasProjectRolePermission($user, ?Project $project, string $permission): bool
    {
        if (!$user || !$project || !in_array($permission, self::PROJECT_ROLE_PERMISSIONS, true)) {
            return false;
        }

        $profileId = $user->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('employee_profile_id', (int) $profileId)
            ->where('is_active', true)
            ->whereHas('role', function (Builder $builder) use ($permission): void {
                $builder->where(function (Builder $query) use ($permission): void {
                    foreach ($this->projectRolePermissionLookupValues($permission) as $lookupValue) {
                        $query->orWhereJsonContains('permissions', $lookupValue);
                    }
                });
            })
            ->exists();

        if (!$user || !$project || !in_array($permission, self::PROJECT_ROLE_PERMISSIONS, true)) {
            return false;
        }

        $profileId = $user->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('employee_profile_id', (int) $profileId)
            ->where('is_active', true)
            ->whereHas('role', function (Builder $builder) use ($permission): void {
                $builder->whereJsonContains('permissions', $permission);
            })
            ->exists();
    }

    private function hasSystemProjectMemberManagement($user): bool
    {
        return (bool) $user?->hasPositionCapability(PositionCapability::MANAGE_PROJECT_MEMBERS);
    }

    private function canAddProjectMember($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'add_project_member');
    }

    private function canUpdateProjectMemberRole($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_project_member_role');
    }

    private function canRemoveProjectMember($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'remove_project_member');
    }

    private function canManageProjectMembers($user, ?Project $project = null): bool
    {
        return $this->canAddProjectMember($user, $project)
            || $this->canUpdateProjectMemberRole($user, $project)
            || $this->canRemoveProjectMember($user, $project);

        if (!$user) {
            return false;
        }

        return $user->hasPositionCapability(PositionCapability::MANAGE_PROJECT_MEMBERS)
            || $this->userHasProjectRolePermission($user, $project, 'manage_members');
    }

    private function canManageProjectRoles($user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasPositionCapability(PositionCapability::MANAGE_PROJECT_ROLES);
    }

    private function canCreateImplementationDetail($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'create_implementation_detail');
    }

    private function canUpdateImplementationDetail($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_detail');
    }

    private function canDeleteImplementationDetail($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'delete_implementation_detail');
    }

    private function canCreateImplementationSubtask($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'create_implementation_subtask');
    }

    private function canUpdateImplementationSubtask($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_subtask');
    }

    private function canDeleteImplementationSubtask($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'delete_implementation_subtask');
    }

    private function canUpdateImplementationSubtaskStatus($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_subtask_status');
    }

    private function canToggleImplementationDetailLock($user, ?Project $project = null): bool
    {
        return $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'toggle_implementation_detail_lock');
    }

    private function canManageImplementationDetails($user, ?Project $project = null): bool
    {
        return $this->canCreateImplementationDetail($user, $project)
            || $this->canUpdateImplementationDetail($user, $project)
            || $this->canDeleteImplementationDetail($user, $project)
            || $this->canToggleImplementationDetailLock($user, $project);

        return $this->canManageProjectMembers($user)
            || $this->userHasProjectRolePermission($user, $project, 'manage_implementation_details');
    }

    private function canManageImplementationSubtasks($user, ?Project $project = null): bool
    {
        return $this->canCreateImplementationSubtask($user, $project)
            || $this->canUpdateImplementationSubtask($user, $project)
            || $this->canDeleteImplementationSubtask($user, $project)
            || $this->canUpdateImplementationSubtaskStatus($user, $project);
    }

    private function canViewOwnProjects($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->canViewAllProjects($user)) {
            return true;
        }

        return (bool) $user->hasPositionCapability(PositionCapability::VIEW_OWN_PROJECTS);
    }

    private function canUpdateImplementationStatus($user, ProjectImplementationDetail $detail): bool
    {
        $project = $detail->project;

        if ($this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_status')) {
            return true;
        }

        if (!$user?->hasPositionCapability(PositionCapability::UPDATE_PROJECT_TASK_STATUS)) {
            return false;
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return (int) $detail->assigned_to === (int) $profileId;

        if ($this->canManageImplementationDetails($user, $project)
            || $this->userHasProjectRolePermission($user, $project, 'update_implementation_status')) {
            return true;
        }

        if (!$user?->hasPositionCapability(PositionCapability::UPDATE_PROJECT_TASK_STATUS)) {
            return false;
        }

        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return (int) $detail->assigned_to === (int) $profileId;
    }

    private function canUploadProjectAttachment($user, Project $project): bool
    {
        return $this->canManageProjects($user)
            || $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'upload_project_attachment');

        return $this->canManageProjects($user)
            || $this->canManageProjectMembers($user, $project)
            || $this->userHasProjectRolePermission($user, $project, 'manage_attachments');
    }

    private function canUploadImplementationAttachment($user, ProjectImplementationDetail $detail): bool
    {
        if ($this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $detail->project, 'upload_implementation_attachment')) {
            return true;
        }

        $profileId = $user?->employeeProfile?->id;

        return (bool) $user?->hasPositionCapability(PositionCapability::UPDATE_PROJECT_TASK_STATUS)
            && $profileId
            && (int) $detail->assigned_to === (int) $profileId;

        return $this->canManageImplementationDetails($user, $detail->project) || $this->canUpdateImplementationStatus($user, $detail);
    }

    private function canCommentOnImplementationDetail($user, Project $project, ProjectImplementationDetail $detail): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->canManageProjects($user) || $this->canManageImplementationDetails($user, $project)) {
            return true;
        }

        return $this->isActiveProjectMember($user, $project);
    }

    private function canViewProjectAttachment($user, Project $project): bool
    {
        if ($this->canViewAllProjects($user) || $this->canManageProjects($user) || $this->canManageProjectMembers($user, $project)) {
            return true;
        }

        return $this->isActiveProjectMember($user, $project);
    }

    private function canDeleteProjectAttachment($user, Project $project, ProjectAttachment $attachment): bool
    {
        if ($this->canManageProjects($user)
            || $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'delete_project_attachment')) {
            return true;
        }

        return $user && (int) $attachment->uploaded_by === (int) $user->id && $this->canViewProjectAttachment($user, $project);

        if ($this->canManageProjects($user)
            || $this->canManageProjectMembers($user, $project)
            || $this->userHasProjectRolePermission($user, $project, 'manage_attachments')) {
            return true;
        }

        return $user && (int) $attachment->uploaded_by === (int) $user->id && $this->canViewProjectAttachment($user, $project);
    }

    private function canDeleteImplementationComment($user, Project $project, ProjectDetailComment $comment): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->canManageProjects($user) || $this->canManageImplementationDetails($user, $project)) {
            return true;
        }

        return (int) $comment->user_id === (int) $user->id
            && $this->isActiveProjectMember($user, $project);
    }

    private function isActiveProjectMember($user, Project $project): bool
    {
        $profileId = $user?->employeeProfile?->id;
        if (!$profileId) {
            return false;
        }

        return ProjectMember::query()
            ->where('project_id', $project->id)
            ->where('employee_profile_id', (int) $profileId)
            ->where('is_active', true)
            ->exists();
    }

    private function ensureAttachmentBelongsToProject(Project $project, ProjectAttachment $attachment): void
    {
        if ((int) $attachment->project_id !== (int) $project->id) {
            abort(404);
        }
    }

    private function ensureImplementationDetailBelongsToProject(Project $project, ProjectImplementationDetail $implementationDetail): void
    {
        if ((int) $implementationDetail->project_id !== (int) $project->id) {
            abort(404);
        }
    }

    private function ensureSubtaskBelongsToDetail(Project $project, ProjectImplementationDetail $implementationDetail, ProjectImplementationSubtask $subtask): void
    {
        $this->ensureImplementationDetailBelongsToProject($project, $implementationDetail);

        if (
            (int) $subtask->project_id !== (int) $project->id
            || (int) $subtask->project_implementation_detail_id !== (int) $implementationDetail->id
        ) {
            abort(404);
        }
    }

    private function ensureMilestoneBelongsToProject(Project $project, ProjectMilestone $milestone): void
    {
        if ((int) $milestone->project_id !== (int) $project->id) {
            abort(404);
        }
    }

    private function ensureImplementationCommentBelongsToDetail(
        Project $project,
        ProjectImplementationDetail $implementationDetail,
        ProjectDetailComment $comment
    ): void {
        if (
            (int) $comment->project_id !== (int) $project->id
            || (int) $comment->implementation_detail_id !== (int) $implementationDetail->id
        ) {
            abort(404);
        }
    }

    private function canEditImplementationSchedule($user, ?Project $project = null): bool
    {
        return (bool) $user?->hasPositionCapability(PositionCapability::MANAGE_PROJECTS)
            || $this->hasSystemProjectMemberManagement($user)
            || $this->userHasProjectRolePermission($user, $project, 'edit_implementation_schedule');

        return (bool) $user?->hasPositionCapability(PositionCapability::MANAGE_PROJECTS)
            || $this->userHasProjectRolePermission($user, $project, 'manage_implementation_details');
    }
}
