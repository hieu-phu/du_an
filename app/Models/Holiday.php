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
        'is_paid_leave',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_paid_leave' => 'boolean',
    ];
}
