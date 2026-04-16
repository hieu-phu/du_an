<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorityLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'rank',
        'name',
        'is_active',
    ];

    protected $casts = [
        'rank' => 'integer',
        'is_active' => 'boolean',
    ];
}
