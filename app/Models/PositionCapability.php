<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PositionCapability extends Model
{
    protected $fillable = [
        'code',
        'name',
        'module',
        'description',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'position_capability_position', 'capability_id', 'position_id')
            ->withTimestamps();
    }

    public function userOverrides(): HasMany
    {
        return $this->hasMany(UserPositionCapabilityOverride::class, 'capability_id');
    }
}

