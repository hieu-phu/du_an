<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy danh sách user có phân trang + filter.
     */
    public function getListPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with('roles:id,name')->latest()->paginate($perPage);
    }

    /**
     * Tạo user mới.
     */
    public function createUser(array $data): User
    {
        return $this->query()->create($data);
    }

    /**
     * Cập nhật user.
     */
    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Cập nhật trạng thái user.
     */
    public function updateStatus(User $user, string $status): bool
    {
        return $user->update(['status' => $status]);
    }

    /**
     * Tìm user theo email.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', $email)->first();
    }

    /**
     * Tìm user theo username.
     */
    public function findByUsername(string $username): ?User
    {
        return $this->query()->where('username', $username)->first();
    }

    /**
     * Lấy danh sách user đang hoạt động.
     */
    public function getActiveUsers(array $columns = ['*']): Collection
    {
        return $this->query()->where('status', 'active')->get($columns);
    }
}
