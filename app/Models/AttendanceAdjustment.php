<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAdjustment extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceAdjustmentFactory> */
    use HasFactory;

    protected $fillable = [
        'attendance_record_id',
        'approval_request_id',
        'old_check_in_at',
        'new_check_in_at',
        'old_check_out_at',
        'new_check_out_at',
        'reason',
        'status',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'old_check_in_at' => 'datetime',
        'new_check_in_at' => 'datetime',
        'old_check_out_at' => 'datetime',
        'new_check_out_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function approvalRequest(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class);
    }
}
