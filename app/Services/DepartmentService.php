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
    public function index(array $filters = [])
    {
        return $this->departmentRepository->getAllWithManager($filters);
    }

    /**
     * Create a new department.
     */
    public function store(array $data)
    {
        $department = $this->departmentRepository->create($data);
        $this->audit('departments', 'create', "Tao phong ban {$department->name}", 'departments', $department->id);

        return $department;
    }

    /**
     * Update an existing department.
     */
    public function update($id, array $data)
    {
        $result = $this->departmentRepository->update($id, $data);
        $this->audit('departments', 'update', "Cap nhat phong ban #{$id}", 'departments', $id);

        return $result;
    }

    /**
     * Delete a department.
     */
    public function delete($id)
    {
        $result = $this->departmentRepository->delete($id);
        $this->audit('departments', 'delete', "Xoa phong ban #{$id}", 'departments', $id);

        return $result;
    }

    public function toggleStatus(int $id): bool
    {
        $result = $this->departmentRepository->toggleStatus($id);
        $this->audit('departments', 'toggle_status', "Chuyen trang thai phong ban #{$id}", 'departments', $id);

        return $result;
    }
}
