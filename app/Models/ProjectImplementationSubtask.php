<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectImplementationSubtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_implementation_detail_id',
        'project_id',
        'assigned_to',
        'title',
        'description',
        'start_date',
        'duration_days',
        'due_date',
        'actual_end_date',
        'deadline_reminded_at',
        'status',
        'weight_percent',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'actual_end_date' => 'date',
        'deadline_reminded_at' => 'datetime',
        'duration_days' => 'integer',
        'weight_percent' => 'integer',
        'sort_order' => 'integer',
    ];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(ProjectImplementationDetail::class, 'project_implementation_detail_id');
    }

    public function implementationDetail(): BelongsTo
    {
        return $this->detail();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(ProjectWorkLog::class, 'project_implementation_subtask_id');
    }
}
