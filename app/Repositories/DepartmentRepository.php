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
    public function getAllWithManager()
    {
        return $this->model->with('manager')->withCount('employeeProfiles')->latest()->get();
    }
}
