<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_leave_balance_id',
        'attendance_request_id',
        'type',
        'days',
        'balance_after',
        'note',
        'created_by',
    ];

    protected $casts = [
        'days' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function balance(): BelongsTo
    {
        return $this->belongsTo(EmployeeLeaveBalance::class, 'employee_leave_balance_id');
    }

    public function attendanceRequest(): BelongsTo
    {
        return $this->belongsTo(AttendanceRequest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
