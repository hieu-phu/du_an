<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\NewEmployeeAccountCreatedMail;
use App\Models\AuthorityLevel;
use App\Models\Department;
use App\Models\Position;
use App\Models\PositionCapability as PositionCapabilityModel;
use App\Models\SalaryHistory;
use App\Models\User;
use App\Models\UserPositionCapabilityOverride;
use App\Services\ApprovalDecisionNotifier;
use App\Services\NotificationService;
use App\Services\UserApprovalService;
use App\Services\UserService;
use App\Support\AccessMatrix;
use App\Support\PositionCapability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    private const SYSTEM_OWNER_EMAIL = 'gtvbehieu@gmail.com';

    public function __construct(
        protected UserService $userService,
        protected UserApprovalService $userApprovalService,
        protected NotificationService $notificationService,
        protected ApprovalDecisionNotifier $approvalDecisionNotifier,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page', 'department_id', 'hire_date']);
        $filters['max_authority_level'] = $this->resolveActorAuthorityLevel($request->user());
        
        $filters['scope'] = 'accounts';
        $perPage = $request->integer('per_page', 15);

        $users = $this->userService->getListPaginated($filters, $perPage);

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $filters,
            'pageTitle' => 'Danh sách tài khoản',
            'pageKey' => 'accounts',
            'detailUser' => $this->resolveDetailUser($request),
            'departments' => Department::query()->where('is_active', true)->get(['id', 'name']),
            'positions' => $this->resolveAssignablePositions($request->user()),
            'capabilityOptions' => $this->resolveCapabilityOptions(),
            'provinces' => \App\Models\Province::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function employees(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page', 'department_id', 'hire_date']);
        $filters['max_authority_level'] = $this->resolveActorAuthorityLevel($request->user());
        
        $filters['scope'] = 'employees';
        $perPage = $request->integer('per_page', 15);

        $users = $this->userService->getListPaginated($filters, $perPage);

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $filters,
            'pageTitle' => 'Danh sách nhân sự',
            'pageKey' => 'employees',
            'detailUser' => $this->resolveDetailUser($request),
            'departments' => Department::query()->where('is_active', true)->get(['id', 'name']),
            'positions' => $this->resolveAssignablePositions($request->user()),
            'capabilityOptions' => $this->resolveCapabilityOptions(),
            'provinces' => \App\Models\Province::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function upsertCapabilityOverride(Request $request, User $user)
    {
        $this->ensureManageableUser($request->user(), $user);
        $allowedCapabilityCodes = $this->allowedCapabilityCodes();

        $validated = $request->validate([
            'capability_code' => ['required', 'string', 'max:120', \Illuminate\Validation\Rule::in($allowedCapabilityCodes)],
            'effect' => ['required', 'in:allow,deny'],
            'reason' => ['nullable', 'string', 'max:500'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ], [
            'capability_code.required' => 'Vui lòng chọn quyền cần ghi đè.',
            'capability_code.in' => 'Quyền (Capability) không hợp lệ.',
            'effect.required' => 'Vui lòng chọn hiệu lực.',
            'effect.in' => 'Hiệu lực chỉ chấp nhận cho phép (allow) hoặc từ chối (deny).',
            'expires_at.after' => 'Ngày hết hạn phải lớn hơn thời điểm hiện tại.',
        ]);

        $capability = PositionCapabilityModel::query()
            ->where('code', $validated['capability_code'])
            ->first();

        if (!$capability) {
            return back()->withErrors(['error' => 'Quyền (Capability) chưa được khởi tạo trong danh mục.']);
        }

        UserPositionCapabilityOverride::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'capability_id' => $capability->id,
            ],
            [
                'effect' => $validated['effect'],
                'reason' => $validated['reason'] ?? null,
                'expires_at' => $validated['expires_at'] ?? null,
                'created_by' => $request->user()?->id,
            ]
        );

        return back()->with('success', 'Đã cập nhật ghi đè quyền theo người dùng.');
    }

    public function destroyCapabilityOverride(Request $request, User $user, UserPositionCapabilityOverride $override)
    {
        $this->ensureManageableUser($request->user(), $user);

        if ((int) $override->user_id !== (int) $user->id) {
            return back()->withErrors(['error' => 'Bản ghi ghi đè không thuộc người dùng này.']);
        }

        $override->delete();

        return back()->with('success', 'Đã xóa ghi đè quyền theo người dùng.');
    }

    public function employeeRequests(Request $request)
    {
        abort_unless($request->user()?->hasPositionCapability(\App\Support\PositionCapability::MANAGE_EMPLOYEES), 403);

        return Inertia::render('User/RequestStatuses', [
            'approvalSummary' => $this->userApprovalService->getRequesterStatusSummary($request->user()->id),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->assertAssignablePosition($request->user(), (int) ($validated['position_id'] ?? 0));

            if (AccessMatrix::canApproveUserRequests($request->user())) {
                $this->userService->createUser(
                    $validated,
                    $request->file('avatar')
                );

                return redirect()->back()->with('success', 'Tạo nhân sự thành công!');
            }

            if ($this->positionRequiresApproval((int) ($validated['position_id'] ?? 0))) {
                $this->userApprovalService->submitCreateRequest($validated);

                return redirect()->back()->with('success', 'Đã gửi yêu cầu tạo tài khoản Nhân sự cho Admin duyệt.');
            }

            $createdUser = $this->userService->createUser(
                $validated,
                $request->file('avatar')
            );

            $this->notifyAdminsAboutNewEmployeeAccount($request->user(), $createdUser);

            return redirect()->back()->with('success', 'Tạo tài khoản nhân viên thành công!');
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->ensureManageableUser($request->user(), $user);

        try {
            $validated = $request->validated();
            $newPositionId = (int) ($validated['position_id'] ?? 0);
            $currentPositionId = (int) ($user->employeeProfile?->position_id ?? 0);

            if ($newPositionId > 0 && $newPositionId !== $currentPositionId) {
                $this->assertAssignablePosition($request->user(), $newPositionId);
            }

            $this->userService->updateUser(
                $user,
                $validated,
                $request->file('avatar')
            );

            return redirect()->back()->with('success', 'Cập nhật nhân sự thành công!');
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Cập nhật thất bại: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateSalary(Request $request, User $user)
    {
        $this->ensureManageableUser($request->user(), $user);

        $actor = $request->user();

        abort_unless(
            $actor?->hasAnyPositionCapability([
                PositionCapability::MANAGE_SALARY,
                PositionCapability::APPROVE_SALARY_REQUESTS,
            ]),
            403
        );

        $validated = $request->validate([
            'base_salary' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], [
            'base_salary.required' => 'Vui lòng nhập lương cơ bản.',
            'base_salary.numeric' => 'Lương cơ bản phải là số.',
            'base_salary.min' => 'Lương cơ bản không được âm.',
            'reason.max' => 'Lý do không được vượt quá 500 ký tự.',
        ]);

        try {
            $user->loadMissing('employeeProfile');
            $profile = $user->employeeProfile;

            if (!$profile) {
                throw ValidationException::withMessages([
                    'base_salary' => 'Nhân sự này chưa có hồ sơ nhân viên để cập nhật lương.',
                ]);
            }

            $currentSalary = round((float) ($profile->base_salary ?? 0), 2);
            $requestedSalary = round((float) $validated['base_salary'], 2);

            if ($requestedSalary === $currentSalary) {
                throw ValidationException::withMessages([
                    'base_salary' => 'Lương mới trùng với lương hiện tại, không cần cập nhật.',
                ]);
            }

            if (!AccessMatrix::canApproveSalaryRequests($actor)) {
                $this->userApprovalService->submitSalaryChangeRequest(
                    $user,
                    $requestedSalary,
                    $validated['reason'] ?? 'HR đề nghị thay đổi lương cơ bản'
                );

                return redirect()->back()->with('success', 'Đã gửi yêu cầu đổi lương cho Admin duyệt.');
            }

            DB::transaction(function () use ($profile, $requestedSalary, $currentSalary, $actor, $validated, $user) {
                $profile->update([
                    'base_salary' => $requestedSalary,
                ]);

                SalaryHistory::query()->create([
                    'employee_profile_id' => $profile->id,
                    'old_salary' => $currentSalary,
                    'new_salary' => $requestedSalary,
                    'currency' => 'VND',
                    'effective_date' => now()->toDateString(),
                    'approved_by' => $actor?->id,
                    'requested_by' => $actor?->id,
                    'note' => $validated['reason'] ?? 'Cập nhật lương trực tiếp từ danh sách nhân sự',
                ]);

                $this->approvalDecisionNotifier->notifyDirectSalaryUpdate(
                    $user->fresh('employeeProfile'),
                    $currentSalary,
                    $requestedSalary,
                    $validated['reason'] ?? null,
                    $actor
                );
            });

            return redirect()->back()->with('success', 'Đã cập nhật lương cơ bản.');
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Cập nhật lương thất bại: ' . $e->getMessage(),
            ]);
        }
    }
    public function toggleStatus(User $user)
    {
        $this->ensureManageableUser(request()->user(), $user);

        try {
            $this->userService->toggleStatus($user);

            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateAccountStatus(Request $request, User $user)
    {
        $this->ensureManageableUser($request->user(), $user);

        $validated = $request->validate([
            'status' => ['required', 'in:active,inactive,pending,blocked'],
        ]);

        try {
            $this->userService->updateAccountStatus($user, $validated['status']);

            return redirect()->back()->with('success', 'Cập nhật trạng thái tài khoản thành công!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateEmploymentStatus(Request $request, User $user)
    {
        $this->ensureManageableUser($request->user(), $user);

        $validated = $request->validate([
            'employment_status' => ['required', 'in:active,inactive,terminated'],
        ]);

        try {
            $this->userService->updateEmploymentStatus($user, $validated['employment_status']);

            return redirect()->back()->with('success', 'Cập nhật trạng thái làm việc thành công!');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ]);
        }
    }

    private function ensureManageableUser(User $actor, User $target): void
    {
        if ($this->isSystemOwner($actor)) {
            return;
        }

        if ((int) $actor->id === (int) $target->id) {
            return;
        }

        $actorLevel = $this->resolveActorAuthorityLevel($actor);
        $targetLevel = $this->resolveActorAuthorityLevel($target);

        if ($actorLevel <= $targetLevel) {
            abort(403, 'Bạn không được phép tác động tài khoản có mức quyền hạn bằng hoặc cao hơn.');
        }
    }

    private function notifyAdminsAboutNewEmployeeAccount(User $actor, User $createdUser): void
    {
        $adminIds = $this->resolveApproverIds((int) $actor->id);

        if (empty($adminIds)) {
            return;
        }

        $this->notificationService->createForUsers(
            $adminIds,
            'Tài khoản nhân viên mới',
            "{$actor->name} đã tạo tài khoản nhân viên {$createdUser->name}.",
            [
                'user_id' => $createdUser->id,
                'user_name' => $createdUser->name,
                'position_name' => $createdUser->employeeProfile?->position?->name,
                'authority_level' => (int) ($createdUser->employeeProfile?->position?->authority_level ?? 0),
                'action_url' => "/users/employees?detail_user={$createdUser->id}",
            ],
            "/users/employees?detail_user={$createdUser->id}",
            null,
            'user',
            $actor->id,
            User::class,
            $createdUser->id,
        );

        $this->sendAdminEmailsAboutNewEmployeeAccount($actor, $createdUser, $adminIds);
    }

    private function sendAdminEmailsAboutNewEmployeeAccount(User $actor, User $createdUser, array $adminIds): void
    {
        $admins = User::query()
            ->whereIn('id', $adminIds)
            ->whereNotNull('email')
            ->get(['id', 'name', 'email']);

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewEmployeeAccountCreatedMail($admin, $actor, $createdUser));
        }
    }

    private function resolveDetailUser(Request $request): ?array
    {
        $detailUserId = $request->integer('detail_user');

        if (!$detailUserId) {
            return null;
        }

        return $this->userService->getDetailedUser($detailUserId);
    }

    private function resolveCapabilityOptions(): array
    {
        if (Schema::hasTable('position_capabilities')) {
            return PositionCapabilityModel::query()
                ->where('is_active', true)
                ->orderBy('module')
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'module'])
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'module' => $item->module,
                ])
                ->values()
                ->all();
        }

        return collect(PositionCapability::definitions())
            ->map(fn ($meta, $code) => [
                'id' => null,
                'code' => $code,
                'name' => (string) ($meta['name'] ?? $code),
                'module' => (string) ($meta['module'] ?? 'general'),
            ])
            ->values()
            ->all();
    }

    private function allowedCapabilityCodes(): array
    {
        $codes = PositionCapability::all();

        if (Schema::hasTable('position_capabilities')) {
            $dbCodes = PositionCapabilityModel::query()->pluck('code')->all();
            $codes = array_values(array_unique(array_merge($codes, $dbCodes)));
        }

        return $codes;
    }

    private function resolveAssignablePositions(User $actor)
    {
        $actorLevel = $this->resolveActorAuthorityLevel($actor);
        $query = Position::query()
            ->where('is_active', true);

        if (!$this->isSystemOwner($actor)) {
            $query->where('authority_level', '<', $actorLevel);
        }

        return $query->get(['id', 'name', 'authority_level', 'capabilities', 'department_id']);
    }

    private function assertAssignablePosition(User $actor, int $positionId): void
    {
        if ($positionId <= 0) {
            throw ValidationException::withMessages([
                'position_id' => 'Vui lòng chọn chức vụ hợp lệ.',
            ]);
        }

        $position = Position::query()
            ->where('is_active', true)
            ->find($positionId);

        if (!$position) {
            throw ValidationException::withMessages([
                'position_id' => 'Chức vụ không tồn tại hoặc đã bị khóa.',
            ]);
        }

        $actorLevel = $this->resolveActorAuthorityLevel($actor);
        $targetLevel = (int) ($position->authority_level ?? 1);

        if (!$this->isSystemOwner($actor) && $targetLevel >= $actorLevel) {
            throw ValidationException::withMessages([
                'position_id' => 'Bạn chỉ được tạo/gán chức vụ có mức quyền hạn thấp hơn mình.',
            ]);
        }
    }

    private function resolveActorAuthorityLevel(User $actor): int
    {
        $actor->loadMissing('employeeProfile.position');

        if ($this->isSystemOwner($actor)) {
            return $this->resolveMaxAuthorityLevel();
        }

        $positionLevel = (int) ($actor->employeeProfile?->position?->authority_level ?? 0);

        if ($positionLevel > 0) {
            return $positionLevel;
        }

        if (AccessMatrix::canApproveAnyRequest($actor)) {
            return $this->resolveMaxAuthorityLevel();
        }

        return 1;
    }

    private function resolveMaxAuthorityLevel(): int
    {
        if (Schema::hasTable('authority_levels')) {
            $max = (int) (AuthorityLevel::query()
                ->where('is_active', true)
                ->max('rank') ?? 0);

            if ($max > 0) {
                return $max;
            }
        }

        return 5;
    }

    private function isSystemOwner(?User $actor): bool
    {
        return $actor !== null
            && strcasecmp((string) $actor->email, self::SYSTEM_OWNER_EMAIL) === 0;
    }
    private function positionRequiresApproval(int $positionId): bool
    {
        if ($positionId <= 0) {
            return false;
        }

        $position = Position::query()->find($positionId);
        if (!$position) {
            return false;
        }

        $sensitiveCapabilities = [
            PositionCapability::APPROVE_REQUESTS,
            PositionCapability::APPROVE_USER_REQUESTS,
            PositionCapability::APPROVE_DEPARTMENT_REQUESTS,
            PositionCapability::APPROVE_SALARY_REQUESTS,
            PositionCapability::MANAGE_POSITIONS,
            PositionCapability::MANAGE_DEPARTMENTS,
            PositionCapability::MANAGE_SALARY,
        ];

        $resolved = $position->resolvedCapabilities();

        foreach ($sensitiveCapabilities as $capability) {
            if (in_array($capability, $resolved, true)) {
                return true;
            }
        }

        return false;
    }

    private function resolveApproverIds(?int $excludeUserId = null): array
    {
        return User::query()
            ->where('status', 'active')
            ->when($excludeUserId, fn ($query) => $query->whereKeyNot($excludeUserId))
            ->with('employeeProfile.position')
            ->get(['id'])
            ->filter(fn (User $user) => AccessMatrix::canApproveUserRequests($user))
            ->pluck('id')
            ->values()
            ->all();
    }
}
