@php
    $formatMinutes = function ($value): string {
        $minutes = (int) $value;
        if ($minutes <= 0) {
            return '0 phút';
        }
        $hours = intdiv($minutes, 60);
        $remainMinutes = $minutes % 60;
        if ($hours <= 0) {
            return $remainMinutes . ' phút';
        }
        if ($remainMinutes === 0) {
            return $hours . ' giờ';
        }
        return $hours . ' giờ ' . $remainMinutes . ' phút';
    };

    $dayStatusLabel = function ($value): string {
        return match ($value) {
            'present' => 'Đi làm',
            'late' => 'Đi muộn',
            'early_leave' => 'Về sớm',
            'leave' => 'Nghỉ phép',
            'unpaid_leave' => 'Nghỉ không lương',
            'holiday_paid' => 'Lễ có lương',
            'day_off' => 'Nghỉ theo phân ca',
            'business_trip' => 'Công tác',
            'missing_check_in' => 'Thiếu check in',
            'missing_check_out' => 'Thiếu check out',
            'absent' => 'Vắng mặt',
            default => '-',
        };
    };

    $approvalStatusLabel = function ($value): string {
        if ($value === 'needs_verification') {
            return 'Cần xác minh';
        }

        return match ($value) {
            'pending' => 'Chờ duyệt',
            'not_required' => 'Không cần duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default => '-',
        };
    };
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo chấm công PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .meta, .summary { margin-bottom: 12px; }
        .summary span { display: inline-block; margin-right: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Báo cáo chấm công</h1>
    <div class="meta">Kỳ báo cáo: {{ sprintf('%02d/%04d', $filters['month'], $filters['year']) }}</div>

    <div class="summary">
        <span>Tổng bản ghi: {{ $summary['total_records'] }}</span>
        <span>Đã duyệt: {{ $summary['confirmed_records'] }}</span>
        <span>Chờ duyệt: {{ $summary['pending_records'] }}</span>
        <span>Từ chối: {{ $summary['rejected_records'] ?? 0 }}</span>
        <span>Tổng giờ làm: {{ $summary['total_worked_hours'] }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nhân viên</th>
                <th>Mã NV</th>
                <th>Phòng ban</th>
                <th>Ngày công</th>
                <th>Check in</th>
                <th>Check out</th>
                <th>Giờ làm</th>
                <th>Đi muộn</th>
                <th>Về sớm</th>
                <th>Tăng ca</th>
                <th>Trạng thái ngày</th>
                <th>Duyệt</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record['employee_name'] ?? '-' }}</td>
                    <td>{{ $record['employee_code'] ?? '-' }}</td>
                    <td>{{ $record['department_name'] ?? '-' }}</td>
                    <td>{{ $record['work_date'] ?? '-' }}</td>
                    <td>{{ $record['check_in_at'] ?? '-' }}</td>
                    <td>{{ $record['check_out_at'] ?? '-' }}</td>
                    <td>{{ $formatMinutes($record['worked_minutes'] ?? 0) }}</td>
                    <td>{{ $formatMinutes($record['late_minutes'] ?? 0) }}</td>
                    <td>{{ $formatMinutes($record['early_leave_minutes'] ?? 0) }}</td>
                    <td>{{ $formatMinutes($record['overtime_minutes'] ?? 0) }}</td>
                    <td>{{ $record['day_status_label'] ?? $dayStatusLabel($record['day_status'] ?? null) }}</td>
                    <td>{{ $approvalStatusLabel($record['display_approval_status'] ?? $record['approval_status'] ?? null) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">Không có dữ liệu chấm công.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
