<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bang luong cong ty</title>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="6">
        <tr>
            <th colspan="14">BANG LUONG CONG TY {{ sprintf('%02d/%04d', $filters['month'], $filters['year']) }}</th>
        </tr>
        <tr>
            <td>Nhan su trong ky</td>
            <td>{{ $summary['employee_count'] }}</td>
            <td>Tong luong co ban</td>
            <td>{{ number_format((float) $summary['total_base_salary'], 0, ',', '.') }}</td>
            <td>Thu nhap phat sinh</td>
            <td>{{ number_format((float) $summary['total_base_salary_amount'], 0, ',', '.') }}</td>
            <td>Tien OT</td>
            <td>{{ number_format((float) $summary['total_overtime_amount'], 0, ',', '.') }}</td>
            <td>Phu cap</td>
            <td>{{ number_format((float) ($summary['total_allowance_amount'] ?? 0), 0, ',', '.') }}</td>
            <td>Tong khau tru tam tinh</td>
            <td>{{ number_format((float) ($summary['total_deduction_amount'] ?? 0), 0, ',', '.') }}</td>
            <td>So du sau doi tru</td>
            <td>{{ number_format((float) $summary['total_net_amount'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table border="1" cellspacing="0" cellpadding="6" style="margin-top: 16px;">
        <thead>
            <tr>
                <th>Nhan su</th>
                <th>Ma NV</th>
                <th>Phong ban</th>
                <th>Chuc vu</th>
                <th>Luong co ban</th>
                <th>Cong duyet</th>
                <th>OT duyet</th>
                <th>Tien OT</th>
                <th>Phu cap</th>
                <th>Cho duyet</th>
                <th>Khau tru thieu cong</th>
                <th>Tru khac</th>
                <th>Canh bao</th>
                <th>So du sau doi tru</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['name'] ?? '-' }}</td>
                    <td>{{ $row['employee_code'] ?? '-' }}</td>
                    <td>{{ $row['department'] ?? '-' }}</td>
                    <td>{{ $row['position'] ?? '-' }}</td>
                    <td>{{ number_format((float) ($row['summary']['base_salary'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ number_format((float) ($row['summary']['approved_work_units'] ?? 0), 2, ',', '.') }} / {{ number_format((float) ($row['summary']['expected_work_days'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ (int) ($row['summary']['approved_overtime_minutes'] ?? 0) }} phut</td>
                    <td>{{ number_format((float) ($row['summary']['overtime_amount'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ number_format((float) ($row['summary']['allowance_amount'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ number_format((float) ($row['summary']['pending_amount'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ number_format((float) ($row['summary']['attendance_deduction_amount'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ number_format((float) ($row['summary']['manual_deduction_amount'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ (int) ($row['summary']['warning_count'] ?? 0) }}</td>
                    <td>{{ number_format((float) ($row['summary']['net_amount'] ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14">Khong co du lieu luong.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
