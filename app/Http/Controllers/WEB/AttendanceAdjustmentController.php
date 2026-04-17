<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAdjustment;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceAdjustmentController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Attendance/Adjustments', $this->attendanceService->getMyAdjustmentData($request->user()));
    }

    public function approvals(Request $request): Response
    {
        return Inertia::render('Attendance/AdjustmentApprovals', $this->attendanceService->getAdjustmentApprovalsData($request->all()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_record_id' => ['required', 'integer', 'exists:attendance_records,id'],
            'new_check_in_at' => ['nullable', 'date'],
            'new_check_out_at' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $this->attendanceService->submitAttendanceAdjustmentRequest($request->user(), $validated);
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Da gui yeu cau dieu chinh cong.');
    }

    public function approve(Request $request, AttendanceAdjustment $adjustment)
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attendanceService->reviewAttendanceAdjustmentRequest(
                $adjustment,
                $request->user(),
                'approved',
                $validated['note'] ?? null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Da duyet dieu chinh cong.');
    }

    public function reject(Request $request, AttendanceAdjustment $adjustment)
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attendanceService->reviewAttendanceAdjustmentRequest(
                $adjustment,
                $request->user(),
                'rejected',
                $validated['note'] ?? null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Da tu choi dieu chinh cong.');
    }
}

