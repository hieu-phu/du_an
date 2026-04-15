<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceMonthLock extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceMonthLockFactory> */
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'department_id',
        'is_locked',
        'locked_at',
        'locked_by',
        'unlocked_at',
        'unlocked_by',
        'note',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
