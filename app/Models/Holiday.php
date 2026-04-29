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

    /**
     * Get the display name for the holiday.
     * Non-lunar holidays will have the year suffix removed.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->holiday_name;

        // Detect Lunar holidays: "Tết Nguyên Đán", "Giỗ tổ Hùng Vương", "Tết Âm"
        $isLunar = preg_match('/Tết (Nguyên Đán|Âm)|Giỗ tổ Hùng Vương/i', $name);

        if ($isLunar) {
            return $name;
        }

        // Remove year at the end (e.g., " 2026")
        return preg_replace('/\s+\d{4}$/', '', $name);
    }
}
