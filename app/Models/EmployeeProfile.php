<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeProfile extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'department_id',
        'position_id',
        'default_work_shift_id',
        'province_id',
        'ward_id',
        'address_line',
        'date_of_birth',
        'hire_date',
        'termination_date',
        'base_salary',
        'employment_status',
        'employment_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function defaultWorkShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class, 'default_work_shift_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function workShiftAssignments(): HasMany
    {
        return $this->hasMany(EmployeeWorkShiftAssignment::class);
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function salaryAdjustments(): HasMany
    {
        return $this->hasMany(SalaryAdjustment::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }
}
