<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRecord extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'work_shift_id',
        'work_date',
        'check_in_at',
        'check_out_at',
        'check_in_ip_address',
        'check_out_ip_address',
        'check_in_device',
        'check_out_device',
        'worked_minutes',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'missing_check_in',
        'missing_check_out',
        'attendance_status',
        'approval_status',
        'day_status',
        'is_confirmed',
        'confirmed_by',
        'rejected_by',
        'confirmed_at',
        'rejected_at',
        'note',
        'approval_note',
        'shift_snapshot',
    ];

    protected $casts = [
        'work_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'is_confirmed' => 'boolean',
        'missing_check_in' => 'boolean',
        'missing_check_out' => 'boolean',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'shift_snapshot' => 'array',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(AttendanceApproval::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(AttendanceAdjustment::class);
    }
}
