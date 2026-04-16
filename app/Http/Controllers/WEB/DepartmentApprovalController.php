<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Services\DepartmentApprovalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentApprovalController extends Controller
{
    public function __construct(
        protected DepartmentApprovalService $departmentApprovalService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'per_page']);
        $perPage = $request->integer('per_page', 15);

        return Inertia::render('Departments/Approvals', [
            'approvalRequests' => $this->departmentApprovalService->getRequests($filters, $perPage),
            'filters' => $filters,
            'stats' => $this->departmentApprovalService->getRequestStats(),
        ]);
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->departmentApprovalService->approve($approvalRequest, $validated['review_note'] ?? null);

            return redirect()->back()->with('success', 'Đã duyệt yêu cầu phòng ban thành công.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Không thể duyệt yêu cầu phòng ban: ' . $e->getMessage(),
            ]);
        }
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->departmentApprovalService->reject($approvalRequest, $validated['review_note'] ?? null);

            return redirect()->back()->with('success', 'Đã từ chối yêu cầu phòng ban.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Không thể từ chối yêu cầu phòng ban: ' . $e->getMessage(),
            ]);
        }
    }
}
