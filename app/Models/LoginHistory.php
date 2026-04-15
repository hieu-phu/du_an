<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    /** @use HasFactory<\Database\Factories\LoginHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'ip_address',
        'device',
        'browser',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
