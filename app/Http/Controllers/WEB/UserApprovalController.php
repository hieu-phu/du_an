<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Services\UserApprovalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserApprovalController extends Controller
{
    public function __construct(
        protected UserApprovalService $userApprovalService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'per_page']);
        $perPage = $request->integer('per_page', 15);

        return Inertia::render('User/Approvals', [
            'approvalRequests' => $this->userApprovalService->getCreateUserRequests($filters, $perPage),
            'filters' => $filters,
        ]);
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->userApprovalService->approve($approvalRequest, $validated['review_note'] ?? null);

            return redirect()->back()->with('success', 'Đã duyệt và tạo tài khoản thành công.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Không thể duyệt yêu cầu: ' . $e->getMessage(),
            ]);
        }
    }

    public function reject(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->userApprovalService->reject($approvalRequest, $validated['review_note'] ?? null);

            return redirect()->back()->with('success', 'Đã từ chối yêu cầu tạo tài khoản.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Không thể từ chối yêu cầu: ' . $e->getMessage(),
            ]);
        }
    }
}
