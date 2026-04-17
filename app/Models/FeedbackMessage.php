<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackMessage extends Model
{
    /** @use HasFactory<\Database\Factories\FeedbackMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'receiver_position_id',
        'receiver_group',
        'subject',
        'message',
        'status',
        'read_at',
        'is_replied',
        'reply_message',
        'replied_by',
        'replied_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'is_replied' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function receiverPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'receiver_position_id');
    }

    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedbackReply::class)->latest('id');
    }
}
