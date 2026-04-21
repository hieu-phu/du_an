<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalarySnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_profile_id',
        'employee_code',
        'employee_name',
        'department_name',
        'position_name',
        'currency',
        'base_salary',
        'approved_work_units',
        'approved_overtime_minutes',
        'base_salary_amount',
        'overtime_amount',
        'allowance_amount',
        'pending_amount',
        'attendance_deduction_amount',
        'manual_deduction_amount',
        'deduction_amount',
        'net_amount',
        'warning_count',
        'payload',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'approved_work_units' => 'decimal:2',
        'base_salary_amount' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'allowance_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'attendance_deduction_amount' => 'decimal:2',
        'manual_deduction_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'warning_count' => 'integer',
        'payload' => 'array',
    ];

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }
}
