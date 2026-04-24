<?php
$currency = $profile['currency'] ?? 'VND';
$money = fn ($value) => number_format((float) ($value ?? 0), 0, ',', '.') . ' ' . $currency;
$number = fn ($value, $decimals = 2) => number_format((float) ($value ?? 0), $decimals, ',', '.');
$minutes = fn ($value) => ((int) ($value ?? 0)) . ' phut';
$statusLabel = fn (?string $status) => match ($status) {
    'approved' => 'Da duyệt',
    'rejected' => 'Từ chối',
    default => 'Cho duyệt',
};
$dayLabel = fn (?string $status) => match ($status) {
    'present' => 'Di lam',
    'late' => 'Đi muộn',
    'early_leave' => 'Về sớm',
    'leave' => 'Nghỉ phép',
    'unpaid_leave' => 'Nghỉ không lương',
    'holiday_paid' => 'Le có lương',
    'business_trip' => 'Cong tac',
    'missing_check_in' => 'Thieu check in',
    'missing_check_out' => 'Thieu check out',
    'absent' => 'Vang',
    default => 'Không xác định',
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phieu luong ca nhan</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 11px; line-height: 1.45; }
        h1, h2, h3, p { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px 7px; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; }
        .header { background: #0f172a; color: #fff; padding: 18px 20px; border-radius: 10px; }
        .brand { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #cbd5e1; }
        .title { font-size: 22px; font-weight: 700; margin-top: 6px; }
        .period { color: #cbd5e1; margin-top: 4px; }
        .net-box { float: right; border: 1px solid rgba(255,255,255,.22); border-radius: 10px; padding: 10px 12px; min-width: 170px; text-align: right; background: rgba(255,255,255,.08); }
        .net-label { color: #cbd5e1; font-size: 10px; text-transform: uppercase; }
        .net-value { color: #6ee7b7; font-size: 20px; font-weight: 700; margin-top: 4px; }
        .section { margin-top: 14px; }
        .section-title { font-size: 13px; font-weight: 700; margin-bottom: 8px; color: #0f172a; }
        .grid td { width: 33.33%; border: 0; padding: 0 6px 0 0; }
        .card { border: 1px solid #d1d5db; border-radius: 10px; padding: 10px; min-height: 94px; }
        .muted { color: #6b7280; }
        .strong { font-weight: 700; }
        .amount { text-align: right; white-space: nowrap; }
        .green { color: #047857; }
        .red { color: #dc2626; }
        .summary-table td:first-child { color: #6b7280; }
        .summary-table td:last-child { text-align: right; font-weight: 700; }
        .signature td { border: 0; text-align: center; padding-top: 24px; }
        .signature-line { border-top: 1px solid #9ca3af; padding-top: 8px; margin: 52px 30px 0; }
        .note { color: #6b7280; font-size: 10px; margin-top: 8px; }
    </style>
</head>
<body>
<div class="header">
    <div class="net-box">
        <div class="net-label">Số dư sau doi tru</div>
        <div class="net-value"><?= e($money($summary['net_amount'] ?? 0)) ?></div>
    </div>
    <div class="brand">HRM System</div>
    <div class="title">Phieu luong ca nhan</div>
    <div class="period">Kỳ lương: Thang <?= e(sprintf('%02d/%04d', $filters['month'], $filters['year'])) ?></div>
    <div class="period">Trạng thái ky: <?= e($periodStatus['status_label'] ?? '-') ?></div>
</div>

<div class="section">
    <table class="grid">
        <tr>
            <td>
                <div class="card">
                    <div class="section-title">Nhân viên</div>
                    <div><span class="muted">Ma NV:</span> <span class="strong"><?= e($profile['employee_code'] ?? '-') ?></span></div>
                    <div><span class="muted">Họ tên:</span> <span class="strong"><?= e($profile['name'] ?? '-') ?></span></div>
                    <div><span class="muted">Phòng ban:</span> <?= e($profile['department'] ?? '-') ?></div>
                    <div><span class="muted">Chức vụ:</span> <?= e($profile['position'] ?? '-') ?></div>
                    <div><span class="muted">Loai HD:</span> <?= e($profile['employment_type'] ?? '-') ?></div>
                </div>
            </td>
            <td>
                <div class="card">
                    <div class="section-title">Thu nhap</div>
                    <table class="summary-table">
                        <tr><td>Lương cơ bản</td><td><?= e($money($summary['base_salary'] ?? 0)) ?></td></tr>
                        <tr><td>Thu nhap theo cong</td><td><?= e($money($summary['base_salary_amount'] ?? 0)) ?></td></tr>
                        <tr><td>Tien tang ca</td><td><?= e($money($summary['overtime_amount'] ?? 0)) ?></td></tr>
                        <tr><td>Phu cap</td><td><?= e($money($summary['allowance_amount'] ?? 0)) ?></td></tr>
                        <tr><td>Tổng thu nhap</td><td><?= e($money($summary['gross_amount'] ?? 0)) ?></td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="card">
                    <div class="section-title">Cong va khau tru</div>
                    <table class="summary-table">
                        <tr><td>Cong duyệt</td><td><?= e($number($summary['approved_work_units'] ?? 0)) ?> / <?= e($number($summary['expected_work_days'] ?? 0, 0)) ?></td></tr>
                        <tr><td>Tang ca duyệt</td><td><?= e($minutes($summary['approved_overtime_minutes'] ?? 0)) ?></td></tr>
                        <tr><td>Tien cho duyệt</td><td><?= e($money($summary['pending_amount'] ?? 0)) ?></td></tr>
                        <tr><td>Khau tru thieu cong</td><td class="red"><?= e($money($summary['attendance_deduction_amount'] ?? 0)) ?></td></tr>
                        <tr><td>Khau tru khac</td><td class="red"><?= e($money($summary['manual_deduction_amount'] ?? 0)) ?></td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <h3 class="section-title">Tổng hop tinh luong</h3>
    <table>
        <tbody>
            <tr>
                <th>Don gia ngay</th>
                <th>Don gia gio</th>
                <th>Cong cho duyệt</th>
                <th>Cong chua tinh</th>
                <th>Số dư sau doi tru</th>
            </tr>
            <tr>
                <td class="amount"><?= e($money($summary['daily_rate'] ?? 0)) ?></td>
                <td class="amount"><?= e($money($summary['hourly_rate'] ?? 0)) ?></td>
                <td class="amount"><?= e($number($summary['pending_work_units'] ?? 0)) ?></td>
                <td class="amount"><?= e($number($summary['unpaid_work_units'] ?? 0)) ?></td>
                <td class="amount green strong"><?= e($money($summary['net_amount'] ?? 0)) ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php if (!empty($summary['warnings'])): ?>
    <div class="section">
        <h3 class="section-title">Canh bao dữ liệu</h3>
        <table>
            <tbody>
                <?php foreach ($summary['warnings'] as $warning): ?>
                    <tr><td><?= e($warning) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (count($adjustments ?? []) > 0): ?>
    <div class="section">
        <h3 class="section-title">Phu cap / khau tru khac</h3>
        <table>
            <thead>
                <tr>
                    <th>Loai</th>
                    <th>Nội dung</th>
                    <th>Ghi chú</th>
                    <th class="amount">Số tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adjustments as $adjustment): ?>
                    <tr>
                        <td><?= e(($adjustment['type'] ?? '') === 'allowance' ? 'Phu cap' : 'Khau tru') ?></td>
                        <td><?= e($adjustment['label'] ?? '-') ?></td>
                        <td><?= e($adjustment['note'] ?? '-') ?></td>
                        <td class="amount"><?= e($money($adjustment['amount'] ?? 0)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="section">
    <h3 class="section-title">Cong tinh luong</h3>
    <table>
        <thead>
            <tr>
                <th>Ngay</th>
                <th>Ca ap dung</th>
                <th>Phut lam</th>
                <th>Cong tinh</th>
                <th class="amount">Tien cong</th>
                <th>Tang ca</th>
                <th class="amount">Tien OT</th>
                <th>Trạng thái</th>
                <th>Duyệt</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($records) > 0): ?>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?= e($record['work_date'] ?? '-') ?></td>
                        <td><?= e($record['shift_name'] ?? '-') ?><br><span class="muted"><?= e($record['shift_time_range'] ?? '-') ?></span></td>
                        <td><?= e($minutes($record['worked_minutes'] ?? 0)) ?></td>
                        <td class="amount"><?= e($number($record['work_unit'] ?? 0)) ?></td>
                        <td class="amount"><?= e($money($record['payable_amount'] ?? 0)) ?></td>
                        <td><?= e($minutes($record['overtime_minutes'] ?? 0)) ?><br><span class="muted"><?= e($record['overtime_type_label'] ?? '-') ?></span></td>
                        <td class="amount"><?= e($money($record['overtime_amount'] ?? 0)) ?></td>
                        <td><?= e($dayLabel($record['day_status'] ?? null)) ?></td>
                        <td><?= e($statusLabel($record['approval_status'] ?? null)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">Không có dữ liệu cong trong ky.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <p class="note">Phieu luong nay duoc tao tu dữ liệu chấm công va điều chỉnh luong trong hệ thống tai thoi diem xuat PDF.</p>
</div>

<div class="section">
    <table class="signature">
        <tr>
            <td>
                <div class="strong">Nhân viên</div>
                <div class="signature-line"><?= e($profile['name'] ?? 'Ky va ghi ro ho ten') ?></div>
            </td>
            <td>
                <div class="strong">Phong nhân sự</div>
                <div class="signature-line">Ky va ghi ro ho ten</div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>


