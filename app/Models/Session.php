<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'login_at',
        'logout_at',
        'device_name',
        'session_type',
        'last_activity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'last_activity' => 'integer',
        ];
    }

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include web sessions.
     */
    public function scopeWeb($query)
    {
        return $query->where('session_type', 'web');
    }

    /**
     * Scope a query to only include mobile sessions.
     */
    public function scopeMobile($query)
    {
        return $query->where('session_type', 'mobile');
    }

    /**
     * Scope a query to only include API sessions.
     */
    public function scopeApi($query)
    {
        return $query->where('session_type', 'api');
    }
}