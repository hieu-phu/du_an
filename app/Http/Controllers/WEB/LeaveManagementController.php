<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Services\LeaveManagementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveManagementController extends Controller
{
    public function __construct(
        protected LeaveManagementService $leaveService
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Leave/Management', $this->leaveService->getManagementData($request->all()));
    }

    public function storeType(Request $request)
    {
        $validated = $this->validateLeaveType($request);
        $this->leaveService->storeLeaveType($validated);

        return back()->with('success', 'Đã tạo loại nghỉ phép.');
    }

    public function updateType(Request $request, LeaveType $leaveType)
    {
        $validated = $this->validateLeaveType($request, $leaveType);
        $this->leaveService->updateLeaveType($leaveType, $validated);

        return back()->with('success', 'Đã cập nhật loại nghỉ phép.');
    }

    public function toggleType(LeaveType $leaveType)
    {
        $this->leaveService->toggleLeaveType($leaveType);

        return back()->with('success', 'Đã đổi trạng thái loại nghỉ phép.');
    }

    public function grantBalance(Request $request)
    {
        $validated = $request->validate([
            'employee_profile_id' => ['required', 'integer', 'exists:employee_profiles,id'],
            'leave_type_id' => ['required', 'integer', 'exists:leave_types,id'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->leaveService->grantBalance($request->user(), $validated);

        return back()->with('success', 'Đã cập nhật quỹ phép nhân viên.');
    }

    public function grantBulk(Request $request)
    {
        $validated = $request->validate([
            'employee_profile_id' => ['nullable', 'integer', 'exists:employee_profiles,id'],
            'leave_type_id' => ['nullable', 'integer', 'exists:leave_types,id'],
            'year' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $count = $this->leaveService->grantBulk($request->user(), $validated);

        return back()->with('success', "Đã đồng bộ {$count} dòng số dư phép.");
    }

    public function adjustBalance(Request $request, EmployeeLeaveBalance $balance)
    {
        $validated = $request->validate([
            'days' => ['required', 'numeric', 'min:-365', 'max:365', 'not_in:0'],
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $this->leaveService->adjustBalance($request->user(), $balance, $validated);

        return back()->with('success', 'Đã điều chỉnh quỹ phép.');
    }

    private function validateLeaveType(Request $request, ?LeaveType $leaveType = null): array
    {
        return $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:30',
            ],
            'name' => ['required', 'string', 'max:255'],
            'is_paid' => ['nullable', 'boolean'],
            'deducts_balance' => ['nullable', 'boolean'],
            'requires_attachment' => ['nullable', 'boolean'],
            'annual_quota' => ['nullable', 'numeric', 'min:0', 'max:365'],
            'prorate_by_hire_date' => ['nullable', 'boolean'],
            'max_days_per_request' => ['nullable', 'numeric', 'min:0.5', 'max:365'],
            'carryover_limit' => ['nullable', 'numeric', 'min:0', 'max:365'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}

