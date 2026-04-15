<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityLogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'description',
        'reference_table',
        'reference_id',
        'ip_address',
        'device',
        'user_agent',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];
}
