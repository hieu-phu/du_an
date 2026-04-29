<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectWorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_implementation_detail_id',
        'project_implementation_subtask_id',
        'employee_profile_id',
        'work_date',
        'hours',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(ProjectImplementationDetail::class, 'project_implementation_detail_id');
    }

    public function subtask(): BelongsTo
    {
        return $this->belongsTo(ProjectImplementationSubtask::class, 'project_implementation_subtask_id');
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
