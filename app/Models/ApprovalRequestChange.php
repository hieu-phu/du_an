<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequestChange extends Model
{
    /** @use HasFactory<\Database\Factories\ApprovalRequestChangeFactory> */
    use HasFactory;

    protected $fillable = [
        'approval_request_id',
        'field_name',
        'old_value',
        'new_value',
    ];

    public function approvalRequest(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class);
    }
}
