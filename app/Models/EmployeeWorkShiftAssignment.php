<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkShiftAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeWorkShiftAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'department_id',
        'work_shift_id',
        'effective_from',
        'effective_to',
        'is_active',
        'note',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }
}
