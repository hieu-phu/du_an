<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'leave_type_id',
        'year',
        'opening_balance',
        'accrued_days',
        'used_days',
        'pending_days',
        'adjusted_days',
        'carryover_expires_on',
    ];

    protected $casts = [
        'year' => 'integer',
        'opening_balance' => 'decimal:2',
        'accrued_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'pending_days' => 'decimal:2',
        'adjusted_days' => 'decimal:2',
        'carryover_expires_on' => 'date',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LeaveBalanceTransaction::class);
    }

    public function getTotalEntitledAttribute(): float
    {
        return (float) $this->opening_balance + (float) $this->accrued_days + (float) $this->adjusted_days;
    }

    public function getAvailableDaysAttribute(): float
    {
        return max(0.0, $this->total_entitled - (float) $this->used_days - (float) $this->pending_days);
    }
}
