<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasStatusScope
{
    /**
     * Scope a query to only include active records.
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive records.
     */
    public function scopeInactive(Builder $query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to include records of a specific status.
     */
    public function scopeOfStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
    }
}
