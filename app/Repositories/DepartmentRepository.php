<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository extends BaseRepository
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all departments with their manager.
     */
    public function getAllWithManager(array $filters = [])
    {
        return $this->model
            ->with('manager:id,name,email')
            ->withCount('employeeProfiles')
            ->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where('name', 'like', '%' . trim($filters['search']) . '%');
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function ($query) use ($filters) {
                $query->where('is_active', $filters['status'] === 'active');
            })
            ->latest()
            ->get();
    }

    public function toggleStatus(int $id): bool
    {
        $department = $this->getByIdOrFail($id);

        return $department->update([
            'is_active' => !$department->is_active,
        ]);
    }
}
