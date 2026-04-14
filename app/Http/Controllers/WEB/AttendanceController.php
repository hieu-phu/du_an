<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function checkIn(Request $request)
    {
        $employeeProfileId = $request->user()->employeeProfile?->id;

        if (!$employeeProfileId) {
            return back()->withErrors(['error' => 'Bạn chưa có hồ sơ nhân sự để thực hiện chấm công!']);
        }

        try {
            $this->attendanceService->checkIn(
                $employeeProfileId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Check-in thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function checkOut(Request $request)
    {
        $employeeProfileId = $request->user()->employeeProfile?->id;

        if (!$employeeProfileId) {
            return back()->withErrors(['error' => 'Bạn chưa có hồ sơ nhân sự để thực hiện chấm công!']);
        }

        try {
            $this->attendanceService->checkOut(
                $employeeProfileId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Check-out thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
