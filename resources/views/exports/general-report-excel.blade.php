<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo tổng hợp</title>
</head>
<body>
    <h2>BÁO CÁO TỔNG HỢP</h2>
    <p>Phạm vi: {{ $scopeLabel }}</p>
    <p>Tháng/Năm: {{ $filters['month'] }}/{{ $filters['year'] }}</p>

    <h3>1. Nhân sự theo phòng ban</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Phòng ban</th>
                <th>Số nhân sự</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeByDepartment as $item)
                <tr>
                    <td>{{ $item['department_name'] }}</td>
                    <td>{{ $item['employee_count'] }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Không có dữ liệu.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>2. Dự án theo trạng thái</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Trạng thái</th>
                <th>Số lượng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projectByStatus as $item)
                <tr>
                    <td>{{ $item['status_label'] }}</td>
                    <td>{{ $item['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>3. Tiến độ dự án</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Dự án</th>
                <th>Trạng thái</th>
                <th>Tiến độ</th>
                <th>Đầu việc</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projectProgress as $item)
                <tr>
                    <td>{{ $item['project_name'] }}</td>
                    <td>{{ $item['status_label'] }}</td>
                    <td>{{ $item['progress_percent'] }}%</td>
                    <td>{{ $item['completed_tasks'] }}/{{ $item['total_tasks'] }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Không có dữ liệu.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>4. Chấm công tháng</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr><td>Tổng bản ghi</td><td>{{ $attendanceMonthly['total_records'] ?? 0 }}</td></tr>
        <tr><td>Đúng giờ</td><td>{{ $attendanceMonthly['on_time_records'] ?? 0 }}</td></tr>
        <tr><td>Đi muộn</td><td>{{ $attendanceMonthly['late_records'] ?? 0 }}</td></tr>
        <tr><td>Về sớm</td><td>{{ $attendanceMonthly['early_leave_records'] ?? 0 }}</td></tr>
        <tr><td>Vắng mặt</td><td>{{ $attendanceMonthly['absent_records'] ?? 0 }}</td></tr>
        <tr><td>Tổng phút làm</td><td>{{ $attendanceMonthly['worked_minutes'] ?? 0 }}</td></tr>
    </table>
</body>
</html>


