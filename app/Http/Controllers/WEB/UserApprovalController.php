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
        $filters = $request->only(['status', 'request_type', 'per_page']);
        $perPage = $request->integer('per_page', 15);

        return Inertia::render('User/Approvals', [
            'approvalRequests' => $this->userApprovalService->getApprovalRequests($filters, $perPage),
            'filters' => $filters,
            'stats' => $this->userApprovalService->getApprovalStats(),
        ]);
    }

    public function approve(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->userApprovalService->approve($approvalRequest, $validated['review_note'] ?? null);

            return redirect()->back()->with('success', 'Da duyet yeu cau thanh cong.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Khong the duyet yeu cau: ' . $e->getMessage(),
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

            return redirect()->back()->with('success', 'Da tu choi yeu cau.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Khong the tu choi yeu cau: ' . $e->getMessage(),
            ]);
        }
    }

    public function cancel(Request $request, ApprovalRequest $approvalRequest)
    {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->userApprovalService->cancel($approvalRequest, $request->user(), $validated['review_note'] ?? null);

            return redirect()->back()->with('success', 'Da huy yeu cau.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Khong the huy yeu cau: ' . $e->getMessage(),
            ]);
        }
    }
}
