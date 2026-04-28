<?php

namespace App\Repositories;

use App\Models\Position;

class PositionRepository extends BaseRepository
{
    public function __construct(Position $model)
    {
        parent::__construct($model);
    }

    public function getAllWithEmployeeCount(?string $search = null, ?string $status = null)
    {
        return $this->model
            ->withCount('employeeProfiles')
            ->with('department:id,name')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->get();
    }
}
