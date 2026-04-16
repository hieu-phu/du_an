<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackReply extends Model
{
    /** @use HasFactory<\Database\Factories\FeedbackReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'feedback_message_id',
        'replied_by',
        'message',
    ];

    public function feedbackMessage(): BelongsTo
    {
        return $this->belongsTo(FeedbackMessage::class);
    }

    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
