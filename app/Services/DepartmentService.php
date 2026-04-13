<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;

class DepartmentService extends BaseService
{
    public function __construct(
        protected DepartmentRepository $departmentRepository
    ) {}

    /**
     * Get all departments for display.
     */
    public function index()
    {
        return $this->departmentRepository->getAllWithManager();
    }

    /**
     * Create a new department.
     */
    public function store(array $data)
    {
        return $this->departmentRepository->create($data);
    }

    /**
     * Update an existing department.
     */
    public function update($id, array $data)
    {
        return $this->departmentRepository->update($id, $data);
    }

    /**
     * Delete a department.
     */
    public function delete($id)
    {
        return $this->departmentRepository->delete($id);
    }
}
