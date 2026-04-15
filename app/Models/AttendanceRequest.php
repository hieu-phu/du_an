<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRequest extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'approval_request_id',
        'request_type',
        'status',
        'request_date',
        'from_date',
        'to_date',
        'from_time',
        'to_time',
        'leave_type',
        'requested_status',
        'attachment_path',
        'reason',
        'applied_at',
        'applied_by',
    ];

    protected $casts = [
        'request_date' => 'date',
        'from_date' => 'date',
        'to_date' => 'date',
        'applied_at' => 'datetime',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function approvalRequest(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class);
    }
}
