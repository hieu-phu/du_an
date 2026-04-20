<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    /** @use HasFactory<\Database\Factories\SalaryHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_profile_id',
        'old_salary',
        'new_salary',
        'currency',
        'effective_date',
        'approved_by',
        'note',
    ];

    protected $casts = [
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
        'effective_date' => 'date',
    ];
}
