<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng Chấm Công PDF</title>
@php
    $month = $filters['month'];
    $year = $filters['year'];
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
    $firstDayPosition = Carbon\Carbon::createFromDate($year, $month, 1)->dayOfWeek; // 0=CN, 1=T2 ... 6=T7
    
    $getDayOfWeek = function($day) use ($month, $year, $dayNames) {
        $date = Carbon\Carbon::createFromDate($year, $month, $day);
        return $dayNames[$date->dayOfWeek];
    };
    
    $getAttendanceMark = function($record) {
        if (!$record) return '';
        
        $status = $record['day_status'] ?? null;
        
        if ($status === 'leave') return 'P';
        if ($status === 'unpaid_leave') return 'Ro';
        if ($status === 'absent') return 'V';
        if ($status === 'holiday_paid') return 'L';
        if ($status === 'day_off') return '';
        
        return 'x';
    };
    
@endphp

<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 0; padding: 10px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 2px; text-align: center; font-size: 10px; }
    .header-company { text-align: left; font-size: 14px; font-weight: bold; color: #00008B; border: none; }
    .header-address { text-align: left; font-size: 11px; border: none; }
    .title-main { background-color: #800080; color: white; font-size: 22px; font-weight: bold; padding: 8px; }
    .title-month { background-color: #FFFF00; font-size: 16px; font-weight: bold; padding: 6px; }
    .header-day { background-color: #800080; color: white; font-weight: bold; }
    .header-day-name { background-color: #FFFF00; font-weight: bold; }
    .header-summary { background-color: #FFFF00; font-weight: bold; }
    .department-row { background-color: #FFFF00; font-weight: bold; }
    .name-col { text-align: left; width: 150px; }
    .stt-col { width: 25px; }
    .total-footer { background-color: #FFFF00; font-weight: bold; }
    .signature-section td { border: none; padding-top: 15px; font-weight: bold; font-size: 11px; }
</style>
</head>
<body>

<table>
    <tr>
        <td colspan="{{ $daysInMonth + 8 }}" class="title-main">BẢNG CHẤM CÔNG</td>
    </tr>
    <tr>
        <td colspan="{{ $daysInMonth + 8 }}" class="title-month">Tháng {{ $month }} năm {{ $year }}</td>
    </tr>
    
    <tr>
        <th class="header-day stt-col">STT</th>
        <th class="header-day name-col">Họ Và Tên</th>
        @for($day = 1; $day <= $daysInMonth; $day++)
            <th class="header-day">{{ $day }}</th>
        @endfor
        <th class="header-summary">Ngày Công Đi Làm</th>
        <th class="header-summary">Ngày Nghỉ Hưởng nguyên Lương</th>
        <th class="header-summary">Ngày ghi không lương</th>
        <th class="header-summary">Vắng tự ý nghỉ</th>
        <th class="header-summary">Số lần VP</th>
        <th class="header-summary">Chi tiết vi phạm</th>
    </tr>
    
    <tr>
        <td></td>
        <td></td>
        @for($day = 1; $day <= $daysInMonth; $day++)
            <td class="header-day-name">{{ $getDayOfWeek($day) }}</td>
        @endfor
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    @php
        $stt = 1;
        $grandTotalWork = 0;
        $grandTotalPaidLeave = 0;
        $grandTotalUnpaidLeave = 0;
        $grandTotalAbsent = 0;
        $grandTotalViolation = 0;
    @endphp
    
    @foreach($records->groupBy('department_name') as $department => $departmentRecords)
        @php
            $deptTotalWork = 0;
            $deptTotalPaidLeave = 0;
            $deptTotalUnpaidLeave = 0;
            $deptTotalAbsent = 0;
            $deptTotalViolation = 0;
            
            // ✅ GOM NHÓM DỮ LIỆU THEO NHÂN VIÊN, KHÔNG CHO LẶP LẠI
            $groupedEmployees = [];
            
            foreach ($departmentRecords as $record) {
                $empId = $record['employee_profile_id'];
                $day = (int) Carbon\Carbon::parse($record['work_date'])->day;
                
                if (!isset($groupedEmployees[$empId])) {
                    $groupedEmployees[$empId] = [
                        'employee_name' => $record['employee_name'],
                        'employee_code' => $record['employee_code'] ?? '',
                        'days' => []
                    ];
                }
                
                $groupedEmployees[$empId]['days'][$day] = $record;
            }
        @endphp
        <tr>
            <td class="department-row">{{ chr(64 + $loop->iteration) }}</td>
            <td class="department-row name-col">Bộ Phận {{ $department }}</td>
            @for($day = 1; $day <= $daysInMonth; $day++)
                <td class="department-row"></td>
            @endfor
            <td class="department-row"></td>
            <td class="department-row"></td>
            <td class="department-row"></td>
            <td class="department-row"></td>
            <td class="department-row"></td>
            <td class="department-row"></td>
        </tr>
        
        @foreach($groupedEmployees as $employee)
            @php
                $empDays = $employee['days'] ?? [];
                $workDays = 0;
                $paidLeaveDays = 0;
                $unpaidLeaveDays = 0;
                $absentDays = 0;
                $violationCount = 0;
                $violationDetails = [];
            @endphp
            <tr>
                <td>{{ $stt++ }}</td>
                <td class="name-col">{{ $employee['employee_name'] }}</td>
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $record = $empDays[$day] ?? null;
                        $mark = $getAttendanceMark($record);
                        $workedOnSpecial = $record['worked_on_special_day'] ?? false;
                        
                        if ($mark === 'x') $workDays++;
                        if ($mark === 'P' || $mark === 'L') $paidLeaveDays++;
                        if ($mark === 'Ro') $unpaidLeaveDays++;
                        if ($mark === 'V') $absentDays++;
                        
                        $vStatus = $record['violation_status'] ?? null;
                        if ($vStatus && $vStatus !== 'none' && $vStatus !== '') {
                            $violationCount++;
                            $label = match($vStatus) {
                                'late' => 'Đi muộn',
                                'early_leave' => 'Về sớm',
                                'late_early' => 'Muộn+Sớm',
                                'missing_check_in' => 'Thiếu IN',
                                'missing_check_out' => 'Thiếu OUT',
                                'missing_attendance' => 'Vắng',
                                default => $vStatus
                            };
                            $violationDetails[] = $day . '/' . $month . ': ' . $label;
                        }
                    @endphp
                    <td style="{{ $workedOnSpecial ? 'background-color: #90EE90; font-weight: bold;' : '' }}">
                        {{ $mark }}{{ ($workedOnSpecial && $mark === 'L') ? '+x' : '' }}
                    </td>
                @endfor
                <td>{{ $workDays }}</td>
                <td>{{ $paidLeaveDays }}</td>
                <td>{{ $unpaidLeaveDays }}</td>
                <td>{{ $absentDays }}</td>
                <td>{{ $violationCount }}</td>
                <td style="text-align: left; font-size: 8px;">{{ implode(', ', $violationDetails) }}</td>
            </tr>
            @php
                $deptTotalWork += $workDays;
                $deptTotalPaidLeave += $paidLeaveDays;
                $deptTotalUnpaidLeave += $unpaidLeaveDays;
                $deptTotalAbsent += $absentDays;
                $deptTotalViolation += $violationCount;
            @endphp
        @endforeach
        
        <tr>
            <td class="department-row"></td>
            <td class="department-row name-col"></td>
            @for($day = 1; $day <= $daysInMonth; $day++)
                <td class="department-row"></td>
            @endfor
            <td class="department-row">{{ $deptTotalWork }}</td>
            <td class="department-row">{{ $deptTotalPaidLeave }}</td>
            <td class="department-row">{{ $deptTotalUnpaidLeave }}</td>
            <td class="department-row">{{ $deptTotalAbsent }}</td>
            <td class="department-row">{{ $deptTotalViolation }}</td>
            <td class="department-row"></td>
        </tr>
        
        @php
            $grandTotalWork += $deptTotalWork;
            $grandTotalPaidLeave += $deptTotalPaidLeave;
            $grandTotalUnpaidLeave += $deptTotalUnpaidLeave;
            $grandTotalAbsent += $deptTotalAbsent;
            $grandTotalViolation += $deptTotalViolation;
        @endphp
    @endforeach
    
    <tr>
        <td class="total-footer" colspan="2">TỔNG CỘNG</td>
        @for($day = 1; $day <= $daysInMonth; $day++)
            <td class="total-footer"></td>
        @endfor
        <td class="total-footer">{{ $grandTotalWork }}</td>
        <td class="total-footer">{{ $grandTotalPaidLeave }}</td>
        <td class="total-footer">{{ $grandTotalUnpaidLeave }}</td>
        <td class="total-footer">{{ $grandTotalAbsent }}</td>
        <td class="total-footer">{{ $grandTotalViolation }}</td>
        <td class="total-footer"></td>
    </tr>
</table>

<table style="margin-top: 20px; width: 100%;">
    <tr>
        <td colspan="3" style="border: none; text-align: right; padding-right: 30px; font-size: 11px;">Hà Nội, ngày {{ $daysInMonth }} tháng {{ $month }} năm {{ $year }}</td>
    </tr>
    <tr class="signature-section">
        <td style="width: 33%; text-align: center;">Người lập biểu<br><br><br>(Ký, họ tên)</td>
        <td style="width: 33%; text-align: center;">Kế toán trưởng<br><br><br>(Ký, họ tên)</td>
        <td style="width: 33%; text-align: center;">Giám Đốc Công ty<br><br><br>(Ký, họ tên, đóng dấu)</td>
    </tr>
</table>

<div style="margin-top: 15px; font-size: 11px; font-weight: bold;">
    Ghi chú ký hiệu: 
    <span style="margin-right: 15px;">- x: Đi làm</span>
    <span style="margin-right: 15px;">- P: Nghỉ phép (Hưởng lương)</span>
    <span style="margin-right: 15px;">- L: Nghỉ lễ (Hưởng lương)</span>
    <span style="margin-right: 15px;">- L+x: Đi làm ngày lễ</span>
    <span style="margin-right: 15px;">- Ro: Nghỉ không lương</span>
    <span>- V: Vắng không phép (Tự ý nghỉ)</span>
</div>

</body>
</html>