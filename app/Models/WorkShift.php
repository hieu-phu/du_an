<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkShift extends Model
{
    /** @use HasFactory<\Database\Factories\WorkShiftFactory> */
    use HasFactory;

    protected $fillable = [
        'shift_name',
        'start_time',
        'end_time',
        'standard_minutes',
        'grace_minutes',
        'late_grace_minutes',
        'early_leave_grace_minutes',
        'is_active',
    ];

    protected $casts = [
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
}
