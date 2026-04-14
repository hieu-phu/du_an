<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function implementationDetails(): HasMany
    {
        return $this->hasMany(ProjectImplementationDetail::class);
    }
}
