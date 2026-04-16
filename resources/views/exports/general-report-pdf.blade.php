<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bao cao tong hop PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        h2, h3 { margin: 8px 0; }
    </style>
</head>
<body>
    <h2>BAO CAO TONG HOP</h2>
    <p>Pham vi: {{ $scopeLabel }} | Thang/Nam: {{ $filters['month'] }}/{{ $filters['year'] }}</p>

    <h3>1. Nhan su theo phong ban</h3>
    <table>
        <thead>
            <tr>
                <th>Phong ban</th>
                <th>So nhan su</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeByDepartment as $item)
                <tr>
                    <td>{{ $item['department_name'] }}</td>
                    <td>{{ $item['employee_count'] }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Khong co du lieu.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>2. Du an theo trang thai</h3>
    <table>
        <thead>
            <tr>
                <th>Trang thai</th>
                <th>So luong</th>
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

    <h3>3. Tien do du an</h3>
    <table>
        <thead>
            <tr>
                <th>Du an</th>
                <th>Trang thai</th>
                <th>Tien do</th>
                <th>Dau viec</th>
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
                <tr><td colspan="4">Khong co du lieu.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>4. Cham cong thang</h3>
    <table>
        <tbody>
            <tr><td>Tong ban ghi</td><td>{{ $attendanceMonthly['total_records'] ?? 0 }}</td></tr>
            <tr><td>Dung gio</td><td>{{ $attendanceMonthly['on_time_records'] ?? 0 }}</td></tr>
            <tr><td>Di muon</td><td>{{ $attendanceMonthly['late_records'] ?? 0 }}</td></tr>
            <tr><td>Ve som</td><td>{{ $attendanceMonthly['early_leave_records'] ?? 0 }}</td></tr>
            <tr><td>Vang mat</td><td>{{ $attendanceMonthly['absent_records'] ?? 0 }}</td></tr>
            <tr><td>Tong phut lam</td><td>{{ $attendanceMonthly['worked_minutes'] ?? 0 }}</td></tr>
        </tbody>
    </table>
</body>
</html>
