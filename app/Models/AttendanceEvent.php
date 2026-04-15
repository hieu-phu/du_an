<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceEventFactory> */
    use HasFactory;

    protected $fillable = [
        'attendance_record_id',
        'employee_profile_id',
        'event_type',
        'event_at',
        'source',
        'ip_address',
        'device_info',
        'note',
        'created_by',
    ];

    protected $casts = [
        'event_at' => 'datetime',
    ];

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }
}
