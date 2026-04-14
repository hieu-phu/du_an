<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService extends BaseService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getListPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getListPaginated($filters, $perPage);
    }

    public function getDetailedUser(int $id): ?array
    {
        return $this->userRepository->findDetailedById($id);
    }

    public function createUser(array $validatedData, ?UploadedFile $avatarFile = null): User
    {
        return $this->handleTransaction(function () use ($validatedData, $avatarFile) {
            $validatedData = $this->normalizeEmploymentData($validatedData);
            $avatarPath = $avatarFile ? $this->handleAvatarUpload($avatarFile) : null;

            $user = $this->userRepository->createUser([
                'name' => $validatedData['name'],
                'username' => $validatedData['email'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
                'address' => $validatedData['address'] ?? null,
                'status' => $validatedData['status'],
                'is_employee' => 1,
                'avatar' => $avatarPath,
                'thumbnail' => $avatarPath,
                'creater_id' => $this->user()?->id,
                'slug' => Str::slug($validatedData['name']) . '-' . Str::random(6),
            ]);

            $user->employeeProfile()->create([
                'employee_code' => $this->generateEmployeeCode(),
                'department_id' => $validatedData['department_id'],
                'position_id' => $validatedData['position_id'] ?? null,
                'province_id' => $validatedData['province_id'] ?? null,
                'district_id' => $validatedData['district_id'] ?? null,
                'ward_id' => $validatedData['ward_id'] ?? null,
                'address_line' => $validatedData['address_line'] ?? null,
                'date_of_birth' => $validatedData['date_of_birth'] ?? null,
                'hire_date' => $validatedData['hire_date'],
                'termination_date' => $validatedData['termination_date'] ?? null,
                'base_salary' => $validatedData['base_salary'] ?? 0,
                'employment_status' => $validatedData['employment_status'],
                'employment_type' => $validatedData['employment_type'],
            ]);

            $user->syncRoles([$validatedData['role_name'] ?? 'employee']);

            return $user;
        });
    }

    public function updateUser(User $user, array $validatedData, ?UploadedFile $avatarFile = null): bool
    {
        return $this->handleTransaction(function () use ($user, $validatedData, $avatarFile) {
            $validatedData = $this->normalizeEmploymentData($validatedData);
            $avatarPath = $user->avatar;

            if ($avatarFile) {
                $avatarPath = $this->handleAvatarUpload($avatarFile);
            }

            $userData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'username' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'] ?? null,
                'status' => $validatedData['status'],
                'avatar' => $avatarPath,
                'thumbnail' => $avatarPath,
            ];

            if (!empty($validatedData['password'])) {
                $userData['password'] = Hash::make($validatedData['password']);
            }

            $result = $this->userRepository->updateUser($user, $userData);

            $user->employeeProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_code' => $user->employeeProfile?->employee_code ?? $this->generateEmployeeCode(),
                    'department_id' => $validatedData['department_id'],
                    'position_id' => $validatedData['position_id'] ?? null,
                    'province_id' => $validatedData['province_id'] ?? null,
                    'district_id' => $validatedData['district_id'] ?? null,
                    'ward_id' => $validatedData['ward_id'] ?? null,
                    'address_line' => $validatedData['address_line'] ?? null,
                    'date_of_birth' => $validatedData['date_of_birth'] ?? null,
                    'hire_date' => $validatedData['hire_date'],
                    'termination_date' => $validatedData['termination_date'] ?? null,
                    'base_salary' => $validatedData['base_salary'] ?? 0,
                    'employment_status' => $validatedData['employment_status'],
                    'employment_type' => $validatedData['employment_type'],
                ]
            );

            $user->syncRoles([$validatedData['role_name'] ?? 'employee']);

            return $result;
        });
    }

    public function toggleStatus(User $user): bool
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';

        return $this->userRepository->updateStatus($user, $newStatus);
    }

    public function updateAccountStatus(User $user, string $status): bool
    {
        if ($user->employeeProfile?->employment_status === 'terminated') {
            $status = 'blocked';
        }

        return $this->userRepository->updateStatus($user, $status);
    }

    public function updateEmploymentStatus(User $user, string $employmentStatus): bool
    {
        return $this->handleTransaction(function () use ($user, $employmentStatus) {
            $profile = $user->employeeProfile;

            if (!$profile) {
                return false;
            }

            $profile->update([
                'employment_status' => $employmentStatus,
                'termination_date' => $employmentStatus === 'terminated'
                    ? now()->toDateString()
                    : null,
            ]);

            $accountStatus = $employmentStatus === 'terminated'
                ? 'blocked'
                : $user->status;

            return $this->userRepository->updateStatus($user, $accountStatus);
        });
    }

    private function handleAvatarUpload(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    private function normalizeEmploymentData(array $validatedData): array
    {
        if (($validatedData['employment_status'] ?? null) === 'terminated') {
            $validatedData['status'] = 'blocked';
        }

        if (($validatedData['employment_status'] ?? null) !== 'terminated') {
            $validatedData['termination_date'] = null;
        }

        return $validatedData;
    }

    private function generateEmployeeCode(): string
    {
        $maxNumber = \App\Models\EmployeeProfile::query()
            ->pluck('employee_code')
            ->reduce(function (int $carry, ?string $code) {
                if (!$code || !preg_match('/^EMP-(\d+)$/', $code, $matches)) {
                    return $carry;
                }

                return max($carry, (int) $matches[1]);
            }, 0);

        return 'EMP-' . str_pad((string) ($maxNumber + 1), 3, '0', STR_PAD_LEFT);
    }
}
