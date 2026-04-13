<?php
$models = [
    'Province', 'District', 'Ward', 'Department', 'Position', 'EmployeeProfile',
    'AttendanceRecord', 'AttendanceEvent', 'Project', 'ProjectRole', 'ProjectMember',
    'ProjectImplementationDetail', 'ApprovalRequest', 'ApprovalRequestChange', 'FeedbackMessage',
    'SiteSetting', 'LoginHistory', 'EmployeeStatusLog', 'EmployeeDepartmentHistory',
    'EmployeePositionHistory', 'SalaryHistory', 'AttendanceApproval', 'AttendanceMonthlySummary',
    'WorkShift', 'Holiday', 'ProjectProgressHistory', 'ProjectDetailLog', 'EmailLog',
    'ExportHistory', 'ActivityLog'
];

foreach ($models as $m) {
    echo "Creating $m...\n";
    // We only need model and factory
    exec("php artisan make:model $m -f");
}
echo "Done.";
