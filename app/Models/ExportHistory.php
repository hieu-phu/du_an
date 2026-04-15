<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportHistory extends Model
{
    /** @use HasFactory<\Database\Factories\ExportHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'file_type',
        'filter_data',
        'file_path',
        'exported_at',
    ];

    protected $casts = [
        'filter_data' => 'array',
        'exported_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
