<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Services\DepartmentApprovalService;
use App\Services\DepartmentService;
use App\Services\NotificationService;
use App\Support\AccessMatrix;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService,
        protected DepartmentApprovalService $departmentApprovalService,
        protected NotificationService $notificationService,
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $departments = $this->departmentService->index($filters);
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Departments/Index', [
            'departments' => $departments,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validated = $this->validateDepartment($request);

        if (AccessMatrix::canApproveDepartmentRequests($request->user())) {
            $this->departmentService->store($validated);

            return redirect()->back()->with('success', 'Phòng ban đã được tạo thành công.');
        }

        $approvalRequest = $this->departmentApprovalService->submitCreateRequest($validated);
        $this->notifyAdmins(
            'Yêu cầu tạo phòng ban mới',
            ($request->user()?->name ?? 'HR') . ' vừa gửi yêu cầu tạo phòng ban "' . ($validated['name'] ?? '-') . '".',
            $approvalRequest->id
        );

        return redirect()->back()->with('success', 'Yêu cầu tạo phòng ban đã được gửi đến Admin để duyệt.');
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        $department = Department::query()->findOrFail($id);
        $validated = $this->validateDepartment($request, $department->id);

        if (AccessMatrix::canApproveDepartmentRequests($request->user())) {
            $this->departmentService->update($department->id, $validated);

            return redirect()->back()->with('success', 'Phòng ban đã được cập nhật thành công.');
        }

        $approvalRequest = $this->departmentApprovalService->submitUpdateRequest($department, $validated);
        $this->notifyAdmins(
            'Yêu cầu cập nhật phòng ban',
            ($request->user()?->name ?? 'HR') . ' vừa gửi yêu cầu cập nhật phòng ban "' . $department->name . '".',
            $approvalRequest->id
        );

        return redirect()->back()->with('success', 'Yêu cầu cập nhật phòng ban đã được gửi đến Admin để duyệt.');
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        $this->departmentService->delete($id);

        return redirect()->back()->with('success', 'Phòng ban đã được xóa thành công.');
    }

    /**
     * Toggle active status for the specified department.
     */
    public function toggleStatus(Department $department)
    {
        $wasActive = (bool) $department->is_active;

        if (AccessMatrix::canApproveDepartmentRequests(request()->user())) {
            $this->departmentService->toggleStatus($department->id);

            return redirect()->back()->with(
                'success',
                $wasActive
                    ? 'Phòng ban đã được tạm khóa thành công.'
                    : 'Phòng ban đã được kích hoạt lại thành công.'
            );
        }

        $approvalRequest = $this->departmentApprovalService->submitToggleRequest($department);
        $this->notifyAdmins(
            $wasActive ? 'Yêu cầu khóa phòng ban' : 'Yêu cầu mở lại phòng ban',
            (request()->user()?->name ?? 'HR') . ' vừa gửi yêu cầu ' . ($wasActive ? 'khóa' : 'mở lại') . ' phòng ban "' . $department->name . '".',
            $approvalRequest->id
        );

        return redirect()->back()->with(
            'success',
            $wasActive
                ? 'Yêu cầu khóa phòng ban đã được gửi đến Admin để duyệt.'
                : 'Yêu cầu mở lại phòng ban đã được gửi đến Admin để duyệt.'
        );
    }

    private function validateDepartment(Request $request, ?int $ignoreDepartmentId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($ignoreDepartmentId),
            ],
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Tên phòng ban là bắt buộc.',
            'name.max' => 'Tên phòng ban không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên phòng ban này đã tồn tại.',
            'manager_user_id.exists' => 'Trưởng phòng không hợp lệ.',
        ]);
    }

    private function notifyAdmins(string $title, string $message, int $approvalRequestId): void
    {
        $adminIds = User::query()
            ->where('status', 'active')
            ->with('employeeProfile.position')
            ->get(['id'])
            ->filter(fn (User $user) => AccessMatrix::canApproveDepartmentRequests($user))
            ->pluck('id')
            ->values()
            ->all();

        if (empty($adminIds)) {
            return;
        }

        $this->notificationService->createForUsers(
            $adminIds,
            $title,
            $message,
            ['approval_request_id' => $approvalRequestId, 'type' => 'department'],
            route('web.department-approvals.index'),
            category: 'department'
        );
    }
}
