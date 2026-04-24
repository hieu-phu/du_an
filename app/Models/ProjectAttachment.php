<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAttachment extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'implementation_detail_id',
        'uploaded_by',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function implementationDetail(): BelongsTo
    {
        return $this->belongsTo(ProjectImplementationDetail::class, 'implementation_detail_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
