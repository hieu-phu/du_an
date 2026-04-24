<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDetailComment extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectDetailCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'implementation_detail_id',
        'user_id',
        'content',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function implementationDetail(): BelongsTo
    {
        return $this->belongsTo(ProjectImplementationDetail::class, 'implementation_detail_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
