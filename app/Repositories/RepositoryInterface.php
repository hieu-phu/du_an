<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Lấy tất cả bản ghi.
     */
    public function getAll(array $columns = ['*']): Collection;

    /**
     * Lấy bản ghi theo ID.
     */
    public function getById(int $id, array $columns = ['*']): ?Model;

    /**
     * Lấy bản ghi theo ID hoặc throw 404.
     */
    public function getByIdOrFail(int $id, array $columns = ['*']): Model;

    /**
     * Tạo bản ghi mới.
     */
    public function create(array $data): Model;

    /**
     * Cập nhật bản ghi theo ID.
     */
    public function update(int $id, array $data): bool;

    /**
     * Xoá bản ghi theo ID.
     */
    public function delete(int $id): bool;

    /**
     * Phân trang bản ghi.
     */
    public function paginate(int $perPage = 50, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Tìm bản ghi theo field cụ thể.
     */
    public function findByField(string $field, mixed $value, array $columns = ['*']): Collection;
}
