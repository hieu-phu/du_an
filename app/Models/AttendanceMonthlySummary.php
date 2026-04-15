<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceMonthlySummary extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceMonthlySummaryFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'month',
        'year',
        'total_working_days',
        'total_present_days',
        'total_absent_days',
        'total_leave_days',
        'total_unpaid_leave_days',
        'total_business_trip_days',
        'total_late_count',
        'total_early_leave_count',
        'total_overtime_minutes',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }
}
