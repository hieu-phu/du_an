<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeStatusLog extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeStatusLogFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'old_status',
        'new_status',
        'reason',
        'changed_by',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
