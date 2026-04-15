<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgressHistory extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectProgressHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'old_progress',
        'new_progress',
        'changed_at',
        'changed_by',
        'note',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
