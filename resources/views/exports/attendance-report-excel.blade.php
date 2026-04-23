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
    <title>Báo cáo chấm công</title>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="6">
        <tr>
            <th colspan="12">BÁO CÁO CHẤM CÔNG {{ sprintf('%02d/%04d', $filters['month'], $filters['year']) }}</th>
        </tr>
        <tr>
            <td>Tổng bản ghi</td>
            <td>{{ $summary['total_records'] }}</td>
            <td>Đã duyệt</td>
            <td>{{ $summary['confirmed_records'] }}</td>
            <td>Chờ duyệt</td>
            <td>{{ $summary['pending_records'] }}</td>
            <td>Từ chối</td>
            <td>{{ $summary['rejected_records'] ?? 0 }}</td>
            <td>Tổng giờ làm</td>
            <td colspan="3">{{ $summary['total_worked_hours'] }}</td>
        </tr>
    </table>

    <table border="1" cellspacing="0" cellpadding="6" style="margin-top: 16px;">
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
