<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ApprovalRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'request_type',
        'target_type',
        'target_id',
        'requested_by',
        'reviewed_by',
        'status',
        'submitted_at',
        'reviewed_at',
        'reason',
        'review_note',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function changes(): HasMany
    {
        return $this->hasMany(ApprovalRequestChange::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function target(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }
}
