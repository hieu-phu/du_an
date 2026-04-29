<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMilestone extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectMilestoneFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'phase_name',
        'name',
        'description',
        'planned_start_date',
        'planned_end_date',
        'completed_at',
        'deadline_reminded_at',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'completed_at' => 'date',
        'deadline_reminded_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function implementationDetails(): HasMany
    {
        return $this->hasMany(ProjectImplementationDetail::class);
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
