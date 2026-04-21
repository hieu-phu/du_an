<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

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

        if (!empty($filters['max_authority_level'])) {
            $maxRank = (int) $filters['max_authority_level'];
            $query->where(function ($q) use ($maxRank) {
                $q->whereHas('employeeProfile.position', function ($sub) use ($maxRank) {
                    $sub->where('authority_level', '<=', $maxRank);
                })->orWhereDoesntHave('employeeProfile.position');
            });
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
        $relations = [
            'creator:id,name',
            'employeeProfile.department:id,name',
            'employeeProfile.position:id,name,authority_level,capabilities',
            'employeeProfile.province:id,name',
            'employeeProfile.ward:id,name',
        ];

        if (Schema::hasTable('user_position_capability_overrides')) {
            $relations[] = 'positionCapabilityOverrides:id,user_id,capability_id,effect,reason,expires_at,created_by,created_at,updated_at';
            $relations[] = 'positionCapabilityOverrides.capability:id,code,name,module';
        }

        if (Schema::hasTable('position_capabilities') && Schema::hasTable('position_capability_position')) {
            $relations[] = 'employeeProfile.position.capabilitiesCatalog:id,code';
        }

        return $relations;
    }

    private function transformUser(User $user): array
    {
        $profile = $user->employeeProfile;
        $position = $profile?->position;
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
            'ward_id' => $profile?->ward_id,
            'address_line' => $profile?->address_line,
            'department' => $profile?->department ? [
                'id' => $profile->department->id,
                'name' => $profile->department->name,
            ] : null,
            'position' => $profile?->position ? [
                'id' => $profile->position->id,
                'name' => $profile->position->name,
                'authority_level' => $profile->position->authority_level,
                'capabilities' => $profile->position->resolvedCapabilities(),
            ] : null,
            'position_capability_overrides' => Schema::hasTable('user_position_capability_overrides') && $user->relationLoaded('positionCapabilityOverrides')
                ? $user->positionCapabilityOverrides
                    ->map(fn ($override) => [
                        'id' => $override->id,
                        'capability_id' => $override->capability_id,
                        'capability_code' => $override->capability?->code,
                        'capability_name' => $override->capability?->name,
                        'module' => $override->capability?->module,
                        'effect' => $override->effect,
                        'reason' => $override->reason,
                        'expires_at' => $override->expires_at?->format('Y-m-d H:i:s'),
                        'is_expired' => $override->expires_at ? $override->expires_at->lte(now()) : false,
                        'created_by' => $override->created_by,
                    ])
                    ->values()
                    ->all()
                : [],
            'province' => $profile?->province ? [
                'id' => $profile->province->id,
                'name' => $profile->province->name,
            ] : null,
            'ward' => $profile?->ward ? [
                'id' => $profile->ward->id,
                'name' => $profile->ward->name,
            ] : null,
            'companies' => [],
        ];
    }
}
