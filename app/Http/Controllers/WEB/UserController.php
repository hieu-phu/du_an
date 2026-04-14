<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\NewEmployeeAccountCreatedMail;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserApprovalService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UserApprovalService $userApprovalService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page', 'department_id', 'hire_date', 'role']);
        
        if ($request->user()?->hasRole('hr')) {
            $filters['role'] = 'employee';
        }

        $filters['scope'] = 'accounts';
        $perPage = $request->integer('per_page', 15);

        $users = $this->userService->getListPaginated($filters, $perPage);
        $roleNames = $request->user()?->hasRole('admin')
            ? ['admin', 'hr', 'employee']
            : ['hr', 'employee'];

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $filters,
            'pageTitle' => 'Danh sách tài khoản',
            'pageKey' => 'accounts',
            'detailUser' => $this->resolveDetailUser($request),
            'departments' => Department::query()->where('is_active', true)->get(['id', 'name']),
            'positions' => Position::query()->where('is_active', true)->get(['id', 'name']),
            'roles' => Role::query()->whereIn('name', $roleNames)->orderBy('name')->get(['id', 'name']),
            'provinces' => \App\Models\Province::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function employees(Request $request)
    {
        $filters = $request->only(['search', 'status', 'per_page', 'department_id', 'hire_date', 'role']);
        
        if ($request->user()?->hasRole('hr')) {
            $filters['role'] = 'employee';
        }

        $filters['scope'] = 'employees';
        $perPage = $request->integer('per_page', 15);

        $users = $this->userService->getListPaginated($filters, $perPage);
        $roleNames = $request->user()?->hasRole('admin')
            ? ['admin', 'hr', 'employee']
            : ['hr', 'employee'];

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $filters,
            'pageTitle' => 'Danh sách nhân sự',
            'pageKey' => 'employees',
            'detailUser' => $this->resolveDetailUser($request),
            'departments' => Department::query()->where('is_active', true)->get(['id', 'name']),
            'positions' => Position::query()->where('is_active', true)->get(['id', 'name']),
            'roles' => Role::query()->whereIn('name', $roleNames)->orderBy('name')->get(['id', 'name']),
            'provinces' => \App\Models\Province::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function employeeRequests(Request $request)
    {
        abort_unless($request->user()?->hasRole('hr'), 403);

        return Inertia::render('User/RequestStatuses', [
            'approvalSummary' => $this->userApprovalService->getRequesterStatusSummary($request->user()->id),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->user()?->hasRole('admin')) {
                $this->userService->createUser(
                    $validated,
                    $request->file('avatar')
                );

                return redirect()->back()->with('success', 'Tạo nhân sự thành công!');
            }

            if (($validated['role_name'] ?? 'employee') === 'hr') {
                $this->userApprovalService->submitCreateRequest($validated);

                return redirect()->back()->with('success', 'Đã gửi yêu cầu tạo tài khoản Nhân sự cho Admin duyệt.');
            }

            $createdUser = $this->userService->createUser(
                $validated,
                $request->file('avatar')
            );

            $this->notifyAdminsAboutNewEmployeeAccount($request->user(), $createdUser);

            return redirect()->back()->with('success', 'Tạo tài khoản nhân viên thành công!');
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
            $this->userService->updateUser(
                $user,
                $request->validated(),
                $request->file('avatar')
            );

            return redirect()->back()->with('success', 'Cập nhật nhân sự thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Cập nhật thất bại: ' . $e->getMessage(),
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
        if ($actor->hasRole('admin')) {
            return;
        }

        if ($actor->hasRole('hr') && $target->hasRole('admin')) {
            abort(403, 'HR không được phép tác động tài khoản Admin.');
        }
    }

    private function notifyAdminsAboutNewEmployeeAccount(User $actor, User $createdUser): void
    {
        $adminIds = User::role('admin')->pluck('id')->all();

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
                'role_name' => $createdUser->primaryRole(),
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
}
