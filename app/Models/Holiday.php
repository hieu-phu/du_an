<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    /** @use HasFactory<\Database\Factories\HolidayFactory> */
    use HasFactory;

    protected $fillable = [
        'holiday_date',
        'holiday_name',
        'holiday_type',
        'is_paid_leave',
        'is_recurring',
        'note',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_paid_leave' => 'boolean',
        'is_recurring' => 'boolean',
    ];
}
