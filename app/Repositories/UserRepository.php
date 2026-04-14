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

    public function getListPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()
            ->with($this->detailRelations());

        if (!empty($filters['scope']) && $filters['scope'] === 'employees') {
            $query->whereHas('employeeProfile');
        }

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

        if (!empty($filters['department_id'])) {
            $departmentId = $filters['department_id'];
            $query->whereHas('employeeProfile', fn ($q) => $q->where('department_id', $departmentId));
        }

        if (!empty($filters['hire_date'])) {
            $hireDate = $filters['hire_date'];
            $query->whereHas('employeeProfile', fn ($q) => $q->whereDate('hire_date', $hireDate));
        }

        if (!empty($filters['role'])) {
            $role = $filters['role'];
            $query->role($role);
        }

        return $query->latest()->paginate($perPage)->through(fn (User $user) => $this->transformUser($user));
    }

    public function findDetailedById(int $id): ?array
    {
        $user = $this->query()
            ->with($this->detailRelations())
            ->find($id);

        return $user ? $this->transformUser($user) : null;
    }

    public function createUser(array $data): User
    {
        return $this->query()->create($data);
    }

    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function updateStatus(User $user, string $status): bool
    {
        return $user->update(['status' => $status]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', $email)->first();
    }

    public function findByUsername(string $username): ?User
    {
        return $this->query()->where('username', $username)->first();
    }

    public function getActiveUsers(array $columns = ['*']): Collection
    {
        return $this->query()->where('status', 'active')->get($columns);
    }

    private function detailRelations(): array
    {
        return [
            'roles:id,name',
            'creator:id,name',
            'employeeProfile.department:id,name',
            'employeeProfile.position:id,name',
            'employeeProfile.province:id,name',
            'employeeProfile.district:id,name',
            'employeeProfile.ward:id,name',
        ];
    }

    private function transformUser(User $user): array
    {
        $profile = $user->employeeProfile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'status' => $user->status,
            'avatar' => $user->avatar,
            'thumbnail' => $user->thumbnail,
            'last_login_at' => $user->last_login_at,
            'is_employee' => (bool) $user->is_employee,
            'has_employee_profile' => (bool) $profile,
            'role_name' => $user->roles->first()?->name,
            'creator_name' => $user->creator?->name,
            'employee_code' => $profile?->employee_code,
            'date_of_birth' => $profile?->date_of_birth?->format('Y-m-d'),
            'hire_date' => $profile?->hire_date?->format('Y-m-d'),
            'termination_date' => $profile?->termination_date?->format('Y-m-d'),
            'base_salary' => $profile?->base_salary,
            'employment_status' => $profile?->employment_status,
            'employment_type' => $profile?->employment_type,
            'department_id' => $profile?->department_id,
            'position_id' => $profile?->position_id,
            'province_id' => $profile?->province_id,
            'district_id' => $profile?->district_id,
            'ward_id' => $profile?->ward_id,
            'address_line' => $profile?->address_line,
            'department' => $profile?->department ? [
                'id' => $profile->department->id,
                'name' => $profile->department->name,
            ] : null,
            'position' => $profile?->position ? [
                'id' => $profile->position->id,
                'name' => $profile->position->name,
            ] : null,
            'province' => $profile?->province ? [
                'id' => $profile->province->id,
                'name' => $profile->province->name,
            ] : null,
            'district' => $profile?->district ? [
                'id' => $profile->district->id,
                'name' => $profile->district->name,
            ] : null,
            'ward' => $profile?->ward ? [
                'id' => $profile->ward->id,
                'name' => $profile->ward->name,
            ] : null,
            'companies' => [],
        ];
    }
}
