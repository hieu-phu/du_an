<?php

namespace App\Models;

use App\Enums\ApprovalDecision;
use App\Enums\ApprovalDecisionDeliveryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalDecisionDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'dedupe_key',
        'module',
        'channel',
        'decision',
        'status',
        'recipient_user_id',
        'recipient_email',
        'subject',
        'action_url',
        'reference_type',
        'reference_id',
        'triggered_by',
        'payload',
        'attempt_count',
        'queued_at',
        'last_attempt_at',
        'sent_at',
        'failed_at',
        'last_error',
    ];

    protected $casts = [
        'decision' => ApprovalDecision::class,
        'status' => ApprovalDecisionDeliveryStatus::class,
        'payload' => 'array',
        'queued_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function triggerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}
