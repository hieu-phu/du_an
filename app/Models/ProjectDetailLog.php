<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDetailLog extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectDetailLogFactory> */
    use HasFactory;

    protected $fillable = [
        'implementation_detail_id',
        'field_name',
        'old_value',
        'new_value',
        'updated_by',
    ];

    public function implementationDetail(): BelongsTo
    {
        return $this->belongsTo(ProjectImplementationDetail::class, 'implementation_detail_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
