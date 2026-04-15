<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function myAttendance(Request $request): Response
    {
        return Inertia::render('Attendance/My', $this->attendanceService->getMyAttendanceData(
            $request->user(),
            $request->integer('month') ?: null,
            $request->integer('year') ?: null,
        ));
    }

    public function approvals(Request $request): Response
    {
        return Inertia::render('Attendance/Approvals', $this->attendanceService->getApprovalsData($request->all()));
    }

    public function reports(Request $request): Response
    {
        return Inertia::render('Attendance/Reports', $this->attendanceService->getReportData($request->user(), $request->all()));
    }

    public function checkIn(Request $request)
    {
        $employeeProfileId = $request->user()->employeeProfile?->id;

        if (!$employeeProfileId) {
            return back()->withErrors(['error' => 'Ban chua co ho so nhan su de thuc hien cham cong.']);
        }

        try {
            $this->attendanceService->checkIn(
                $employeeProfileId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Check-in thanh cong.');
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function checkOut(Request $request)
    {
        $employeeProfileId = $request->user()->employeeProfile?->id;

        if (!$employeeProfileId) {
            return back()->withErrors(['error' => 'Ban chua co ho so nhan su de thuc hien cham cong.']);
        }

        try {
            $this->attendanceService->checkOut(
                $employeeProfileId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Check-out thanh cong.');
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function confirm(Request $request, AttendanceRecord $attendanceRecord)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attendanceService->confirmAttendance(
                $attendanceRecord,
                $request->user(),
                $request->string('note')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Da xac nhan ngay cong.');
    }

    public function reject(Request $request, AttendanceRecord $attendanceRecord)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attendanceService->rejectAttendance(
                $attendanceRecord,
                $request->user(),
                $request->string('note')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Da tu choi ngay cong.');
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'request_type' => ['required', 'string', 'max:50'],
            'request_date' => ['nullable', 'date'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'from_time' => ['nullable', 'date_format:H:i'],
            'to_time' => ['nullable', 'date_format:H:i'],
            'requested_status' => ['nullable', 'string', 'max:50'],
            'leave_type' => ['nullable', 'string', 'max:20'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $this->attendanceService->submitAttendanceRequest($request->user(), $request->all());
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã gửi đơn chấm công.');
    }

    public function lockMonth(Request $request)
    {
        $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attendanceService->lockMonth(
                $request->user(),
                $request->integer('month'),
                $request->integer('year'),
                $request->string('note')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã khóa bảng công tháng.');
    }

    public function unlockMonth(Request $request)
    {
        $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attendanceService->unlockMonth(
                $request->user(),
                $request->integer('month'),
                $request->integer('year'),
                $request->string('note')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã mở khóa bảng công tháng.');
    }

    public function exportExcel(Request $request)
    {
        $export = $this->attendanceService->exportExcelHtml($request->user(), $request->all());

        return response("\xEF\xBB\xBF" . $export['content'], 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $export['filename'] . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $export = $this->attendanceService->exportPdfHtml($request->user(), $request->all());

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($export['html'], 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $export['filename'] . '"',
        ]);
    }
}
