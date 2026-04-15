<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceApproval extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceApprovalFactory> */
    use HasFactory;

    protected $fillable = [
        'attendance_record_id',
        'approval_type',
        'approved_by',
        'approved_at',
        'status',
        'note',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
