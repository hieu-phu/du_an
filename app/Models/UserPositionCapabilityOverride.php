<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPositionCapabilityOverride extends Model
{
    protected $fillable = [
        'user_id',
        'capability_id',
        'effect',
        'reason',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function capability(): BelongsTo
    {
        return $this->belongsTo(PositionCapability::class, 'capability_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

