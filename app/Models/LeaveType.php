<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_paid',
        'deducts_balance',
        'requires_attachment',
        'annual_quota',
        'prorate_by_hire_date',
        'max_days_per_request',
        'carryover_limit',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'deducts_balance' => 'boolean',
        'requires_attachment' => 'boolean',
        'annual_quota' => 'decimal:2',
        'prorate_by_hire_date' => 'boolean',
        'max_days_per_request' => 'decimal:2',
        'carryover_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function balances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }
}
