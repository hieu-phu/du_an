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
use App\Models\User;
use App\Models\UserPositionCapabilityOverride;
use App\Services\NotificationService;
use App\Services\UserApprovalService;
use App\Services\UserService;
use App\Support\AccessMatrix;
use App\Support\PositionCapability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UserApprovalService $userApprovalService,
        protected NotificationService $notificationService
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
            'capability_code.required' => 'Vui long chon quyen can ghi de.',
            'capability_code.in' => 'Capability khong hop le.',
            'effect.required' => 'Vui long chon hieu luc.',
            'effect.in' => 'Hieu luc chi chap nhan allow hoac deny.',
            'expires_at.after' => 'Ngay het han phai lon hon thoi diem hien tai.',
        ]);

        $capability = PositionCapabilityModel::query()
            ->where('code', $validated['capability_code'])
            ->first();

        if (!$capability) {
            return back()->withErrors(['error' => 'Capability chua duoc khoi tao trong danh muc.']);
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

        return back()->with('success', 'Da cap nhat ghi de quyen theo nguoi dung.');
    }

    public function destroyCapabilityOverride(Request $request, User $user, UserPositionCapabilityOverride $override)
    {
        $this->ensureManageableUser($request->user(), $user);

        if ((int) $override->user_id !== (int) $user->id) {
            return back()->withErrors(['error' => 'Ban ghi ghi de khong thuoc user nay.']);
        }

        $override->delete();

        return back()->with('success', 'Da xoa ghi de quyen theo nguoi dung.');
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

            if (AccessMatrix::canApproveRequests($request->user())) {
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
            $requester = $request->user();
            $salaryRequestSubmitted = false;
            $newPositionId = (int) ($validated['position_id'] ?? 0);
            $currentPositionId = (int) ($user->employeeProfile?->position_id ?? 0);

            if ($newPositionId > 0 && $newPositionId !== $currentPositionId) {
                $this->assertAssignablePosition($request->user(), $newPositionId);
            }

            $hasSalaryInPayload = array_key_exists('base_salary', $validated);
            $currentSalary = (float) ($user->employeeProfile?->base_salary ?? 0);
            $requestedSalary = (float) ($validated['base_salary'] ?? $currentSalary);
            $salaryChanged = $hasSalaryInPayload && round($requestedSalary, 2) !== round($currentSalary, 2);

            if ($requester && !AccessMatrix::canApproveRequests($requester) && $salaryChanged) {
                if (!$requester->hasPositionCapability(\App\Support\PositionCapability::MANAGE_SALARY)) {
                    throw ValidationException::withMessages([
                        'base_salary' => 'Ban khong co quyen de xuat thay doi luong.',
                    ]);
                }

                $this->userApprovalService->submitSalaryChangeRequest(
                    $user,
                    $requestedSalary,
                    'HR de nghi thay doi luong co ban'
                );

                // HR khong duoc sua luong truc tiep: giu nguyen luong hien tai, cho admin duyet.
                $validated['base_salary'] = $currentSalary;
                $salaryRequestSubmitted = true;
            }

            $this->userService->updateUser(
                $user,
                $validated,
                $request->file('avatar')
            );

            $successMessage = $salaryRequestSubmitted
                ? 'Da cap nhat thong tin va gui yeu cau doi luong cho Admin duyet.'
                : 'Cap nhat nhan su thanh cong!';

            return redirect()->back()->with('success', $successMessage);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Cap nhat that bai: ' . $e->getMessage(),
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
        if ((int) $actor->id === (int) $target->id) {
            return;
        }

        $actorLevel = $this->resolveActorAuthorityLevel($actor);
        $targetLevel = $this->resolveActorAuthorityLevel($target);

        if ($actorLevel <= $targetLevel) {
            abort(403, 'Ban khong duoc phep tac dong tai khoan co muc quyen han bang hoac cao hon.');
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

        return Position::query()
            ->where('is_active', true)
            ->where('authority_level', '<', $actorLevel)
            ->get(['id', 'name', 'authority_level', 'capabilities']);
    }

    private function assertAssignablePosition(User $actor, int $positionId): void
    {
        if ($positionId <= 0) {
            throw ValidationException::withMessages([
                'position_id' => 'Vui long chon chuc vu hop le.',
            ]);
        }

        $position = Position::query()
            ->where('is_active', true)
            ->find($positionId);

        if (!$position) {
            throw ValidationException::withMessages([
                'position_id' => 'Chuc vu khong ton tai hoac da bi khoa.',
            ]);
        }

        $actorLevel = $this->resolveActorAuthorityLevel($actor);
        $targetLevel = (int) ($position->authority_level ?? 1);

        if ($targetLevel >= $actorLevel) {
            throw ValidationException::withMessages([
                'position_id' => 'Ban chi duoc tao/gan chuc vu co muc quyen han thap hon minh.',
            ]);
        }
    }

    private function resolveActorAuthorityLevel(User $actor): int
    {
        $actor->loadMissing('employeeProfile.position');
        $positionLevel = (int) ($actor->employeeProfile?->position?->authority_level ?? 0);

        if ($positionLevel > 0) {
            return $positionLevel;
        }

        if (AccessMatrix::canApproveRequests($actor)) {
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
            ->filter(fn (User $user) => AccessMatrix::canApproveRequests($user))
            ->pluck('id')
            ->values()
            ->all();
    }
}
