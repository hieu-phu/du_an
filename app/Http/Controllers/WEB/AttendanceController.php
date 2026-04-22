<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        return Inertia::render('Attendance/Approvals', $this->attendanceService->getApprovalsData($request->all(), $request->user()));
    }

    public function leaveApprovals(Request $request): Response
    {
        return Inertia::render('Attendance/Approvals', $this->attendanceService->getApprovalsData([
            ...$request->all(),
            'only_leave' => true,
        ], $request->user()));
    }

    public function reports(Request $request): Response
    {
        return Inertia::render('Attendance/Reports', $this->attendanceService->getReportData($request->user(), $request->all()));
    }

    public function checkIn(Request $request)
    {
        $employeeProfileId = $request->user()->employeeProfile?->id;

        if (!$employeeProfileId) {
            return back()->withErrors(['error' => 'Bạn chưa có hồ sơ nhân sự để thực hiện chấm công.']);
        }

        try {
            $this->attendanceService->checkIn(
                $employeeProfileId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Check-in thành công.');
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function checkOut(Request $request)
    {
        $employeeProfileId = $request->user()->employeeProfile?->id;

        if (!$employeeProfileId) {
            return back()->withErrors(['error' => 'Bạn chưa có hồ sơ nhân sự để thực hiện chấm công.']);
        }

        try {
            $this->attendanceService->checkOut(
                $employeeProfileId,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Check-out thành công.');
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function confirm(Request $request, AttendanceRecord $attendanceRecord)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
            'resolved_check_out_time' => ['nullable', 'date_format:H:i'],
        ]);

        try {
            $this->attendanceService->confirmAttendance(
                $attendanceRecord,
                $request->user(),
                $request->string('note')->toString() ?: null,
                $request->string('resolved_check_out_time')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã xác nhận ngày công.');
    }

    public function confirmBulk(Request $request)
    {
        $validated = $request->validate([
            'record_ids' => ['required', 'array', 'min:1', 'max:100'],
            'record_ids.*' => ['integer', 'distinct', 'exists:attendance_records,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = $this->attendanceService->confirmAttendanceBulk(
            $validated['record_ids'],
            $request->user(),
            $request->string('note')->toString() ?: null
        );

        if ($result['failed'] > 0) {
            $firstFailure = $result['failures'][0]['message'] ?? 'Mot so ban ghi khong the duyet.';

            return redirect()->back()
                ->with('success', "Da duyet {$result['approved']} ban ghi.")
                ->withErrors(['error' => "{$result['failed']} ban ghi chua duoc duyet: {$firstFailure}"]);
        }

        return redirect()->back()->with('success', "Da duyet {$result['approved']} ban ghi cham cong.");
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

        return redirect()->back()->with('success', 'Đã từ chối ngày công.');
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
            'business_trip_location' => ['nullable', 'string', 'max:255'],
            'make_up_related_leave_date' => ['nullable', 'date'],
            'leave_type_id' => ['nullable', 'integer', 'exists:leave_types,id'],
            'leave_type' => ['nullable', 'string', 'max:20'],
            'leave_duration_type' => ['nullable', 'string', 'in:full_day,half_day,hourly'],
            'leave_hours' => ['nullable', 'numeric', 'min:0.5', 'max:24'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
        ], [
            'required' => ':attribute là bắt buộc.',
            'date' => ':attribute không đúng định dạng ngày.',
            'date_format' => ':attribute không đúng định dạng giờ.',
            'max' => ':attribute vượt quá giới hạn cho phép.',
        ], [
            'request_type' => 'Loại đơn',
            'request_date' => 'Ngày áp dụng',
            'from_date' => 'Từ ngày',
            'to_date' => 'Đến ngày',
            'from_time' => 'Từ giờ',
            'to_time' => 'Đến giờ',
            'requested_status' => 'Trạng thái đề nghị',
            'leave_type' => 'Loại nghỉ',
            'start_at' => 'Bắt đầu tăng ca',
            'end_at' => 'Kết thúc tăng ca',
            'reason' => 'Lý do',
        ]);

        try {
            $payload = $request->all();
            if ($request->hasFile('attachment')) {
                $payload['attachment_path'] = $request->file('attachment')->store('attendance-requests', 'public');
            }

            $this->attendanceService->submitAttendanceRequest($request->user(), $payload);
        } catch (ValidationException $exception) {
            throw $exception;
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

    public function approveRequest(Request $request, ApprovalRequest $approvalRequest)
    {
        $request->validate([
            'note' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        try {
            $this->attendanceService->reviewApprovalRequest(
                $approvalRequest,
                $request->user(),
                'approved',
                $request->string('note')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã duyệt đơn chấm công.');
    }

    public function approveRequestsBulk(Request $request)
    {
        $validated = $request->validate([
            'approval_request_ids' => ['required', 'array', 'min:1', 'max:100'],
            'approval_request_ids.*' => ['integer', 'distinct', 'exists:approval_requests,id'],
            'note' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $result = $this->attendanceService->reviewApprovalRequestsBulk(
            $validated['approval_request_ids'],
            $request->user(),
            'approved',
            $request->string('note')->toString() ?: null
        );

        if ($result['failed'] > 0) {
            $firstFailure = $result['failures'][0]['message'] ?? 'Mot so don khong the duyet.';

            return redirect()->back()
                ->with('success', "Da duyet {$result['processed']} don.")
                ->withErrors(['error' => "{$result['failed']} don chua duoc duyet: {$firstFailure}"]);
        }

        return redirect()->back()->with('success', "Da duyet {$result['processed']} don.");
    }

    public function rejectRequest(Request $request, ApprovalRequest $approvalRequest)
    {
        $request->validate([
            'note' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        try {
            $this->attendanceService->reviewApprovalRequest(
                $approvalRequest,
                $request->user(),
                'rejected',
                $request->string('note')->toString() ?: null
            );
        } catch (\Throwable $exception) {
            return back()->withErrors(['error' => $exception->getMessage()]);
        }

        return redirect()->back()->with('success', 'Đã từ chối đơn chấm công.');
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
