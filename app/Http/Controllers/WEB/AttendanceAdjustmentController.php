<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã áp dụng điều chỉnh công.');
    }
}

