<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackEscalation extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_message_id',
        'from_position_id',
        'to_position_id',
        'escalation_count',
        'escalated_at',
        'reason',
    ];

    protected $casts = [
        'escalation_count' => 'integer',
        'escalated_at' => 'datetime',
    ];

    public function feedbackMessage(): BelongsTo
    {
        return $this->belongsTo(FeedbackMessage::class);
    }

    public function fromPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'from_position_id');
    }

    public function toPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'to_position_id');
    }
}
