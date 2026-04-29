<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectImplementationDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectImplementationDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_milestone_id',
        'assigned_to',
        'content',
        'execution_date',
        'duration_days',
        'expected_end_date',
        'actual_end_date',
        'detail_status',
        'progress_percent',
        'is_locked',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'execution_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'duration_days' => 'integer',
        'progress_percent' => 'integer',
        'is_locked' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'assigned_to');
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'project_milestone_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProjectDetailLog::class, 'implementation_detail_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class, 'implementation_detail_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectDetailComment::class, 'implementation_detail_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(ProjectImplementationSubtask::class, 'project_implementation_detail_id');
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(ProjectWorkLog::class, 'project_implementation_detail_id');
    }
}
