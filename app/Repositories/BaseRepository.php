<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(protected Model $model) {}

    /**
     * Trả về query builder từ model — dùng cho concrete repo xây query phức tạp.
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function getAll(array $columns = ['*']): Collection
    {
        return $this->query()->get($columns);
    }

    public function getById(int $id, array $columns = ['*']): ?Model
    {
        return $this->query()->find($id, $columns);
    }

    public function getByIdOrFail(int $id, array $columns = ['*']): Model
    {
        return $this->query()->findOrFail($id, $columns);
    }

    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->getByIdOrFail($id);

        return $record->update($data);
    }

    public function delete(int $id): bool
    {
        $record = $this->getByIdOrFail($id);

        return $record->delete();
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage, $columns);
    }

    public function findByField(string $field, mixed $value, array $columns = ['*']): Collection
    {
        return $this->query()->where($field, $value)->get($columns);
    }
}
