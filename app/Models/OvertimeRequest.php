<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeRequest extends Model
{
    /** @use HasFactory<\Database\Factories\OvertimeRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'approval_request_id',
        'work_date',
        'start_at',
        'end_at',
        'requested_minutes',
        'approved_minutes',
        'status',
        'reason',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reviewed_at' => 'datetime',
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
