<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDepartmentHistory extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeDepartmentHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'old_department_id',
        'new_department_id',
        'changed_at',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function oldDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'old_department_id');
    }

    public function newDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'new_department_id');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
