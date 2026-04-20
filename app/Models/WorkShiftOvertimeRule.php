<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkShiftOvertimeRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_shift_id',
        'start_time',
        'end_time',
        'hourly_rate',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
    ];

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }
}
