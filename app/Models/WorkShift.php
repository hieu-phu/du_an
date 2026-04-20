<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkShift extends Model
{
    /** @use HasFactory<\Database\Factories\WorkShiftFactory> */
    use HasFactory;

    protected $fillable = [
        'shift_code',
        'shift_name',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'standard_minutes',
        'half_day_minutes',
        'handover_break_minutes',
        'grace_minutes',
        'late_grace_minutes',
        'early_leave_grace_minutes',
        'allows_overtime',
        'is_overnight',
        'description',
        'is_active',
    ];

    protected $casts = [
        'allows_overtime' => 'boolean',
        'is_overnight' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function employeeAssignments(): HasMany
    {
        return $this->hasMany(EmployeeWorkShiftAssignment::class);
    }

    public function overtimeRule(): HasOne
    {
        return $this->hasOne(WorkShiftOvertimeRule::class);
    }
}
