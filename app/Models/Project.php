<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'status',
        'description',
        'is_locked',
        'locked_at',
        'locked_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(ProjectRole::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function implementationDetails(): HasMany
    {
        return $this->hasMany(ProjectImplementationDetail::class);
    }

    public function progressHistories(): HasMany
    {
        return $this->hasMany(ProjectProgressHistory::class);
    }
}
