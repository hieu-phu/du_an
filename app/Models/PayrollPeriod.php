<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'status',
        'locked_at',
        'locked_by',
        'unlocked_at',
        'unlocked_by',
        'note',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(SalarySnapshot::class);
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function unlocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }
}
