<?php

namespace App\Services;

use App\Models\EmployeeDepartmentHistory;
use App\Models\EmployeePositionHistory;
use App\Models\EmployeeStatusLog;
use App\Models\Position;
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
                'ward_id' => $validatedData['ward_id'] ?? null,
                'address_line' => $validatedData['address_line'] ?? null,
                'date_of_birth' => $validatedData['date_of_birth'] ?? null,
                'hire_date' => $validatedData['hire_date'],
                'termination_date' => $validatedData['termination_date'] ?? null,
                'base_salary' => $validatedData['base_salary'] ?? 0,
                'employment_status' => $validatedData['employment_status'],
                'employment_type' => $validatedData['employment_type'],
            ]);

            $this->audit('users', 'create', "Tao tai khoan {$user->email}", 'users', $user->id);

            return $user;
        });
    }

    public function updateUser(User $user, array $validatedData, ?UploadedFile $avatarFile = null): bool
    {
        return $this->handleTransaction(function () use ($user, $validatedData, $avatarFile) {
            $validatedData = $this->normalizeEmploymentData($validatedData);
            $existingProfile = $user->employeeProfile()->first();
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

            $updatedProfile = $user->employeeProfile()->first();
            $this->recordEmploymentHistories($user, $existingProfile, $updatedProfile);

            $this->audit('users', 'update', "Cap nhat tai khoan {$user->email}", 'users', $user->id);

            return $result;
        });
    }

    public function toggleStatus(User $user): bool
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $result = $this->userRepository->updateStatus($user, $newStatus);
        $this->audit('users', 'toggle_status', "Chuyen trang thai tai khoan {$user->email} sang {$newStatus}", 'users', $user->id);

        return $result;
    }

    public function updateAccountStatus(User $user, string $status): bool
    {
        if ($user->employeeProfile?->employment_status === 'terminated') {
            $status = 'blocked';
        }

        $result = $this->userRepository->updateStatus($user, $status);
        $this->audit('users', 'update_account_status', "Cap nhat trang thai tai khoan {$user->email} sang {$status}", 'users', $user->id);

        return $result;
    }

    public function updateEmploymentStatus(User $user, string $employmentStatus): bool
    {
        return $this->handleTransaction(function () use ($user, $employmentStatus) {
            $profile = $user->employeeProfile;

            if (!$profile) {
                return false;
            }

            $oldStatus = (string) ($profile->employment_status ?? '');
            $profile->update([
                'employment_status' => $employmentStatus,
                'termination_date' => $employmentStatus === 'terminated'
                    ? now()->toDateString()
                    : null,
            ]);

            if ($oldStatus !== $employmentStatus) {
                EmployeeStatusLog::query()->create([
                    'employee_profile_id' => $profile->id,
                    'old_status' => $oldStatus ?: null,
                    'new_status' => $employmentStatus,
                    'reason' => 'Updated from user employment status action',
                    'changed_by' => $this->user()?->id,
                ]);
            }

            $accountStatus = $employmentStatus === 'terminated'
                ? 'blocked'
                : $user->status;

            $result = $this->userRepository->updateStatus($user, $accountStatus);
            $this->audit('users', 'update_employment_status', "Cap nhat trang thai lam viec {$user->email} sang {$employmentStatus}", 'users', $user->id);

            return $result;
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

    private function recordEmploymentHistories(User $user, $beforeProfile, $afterProfile): void
    {
        if (!$afterProfile) {
            return;
        }

        $changedBy = $this->user()?->id;

        $oldDepartmentId = (int) ($beforeProfile?->department_id ?? 0);
        $newDepartmentId = (int) ($afterProfile->department_id ?? 0);
        if ($oldDepartmentId !== $newDepartmentId && $newDepartmentId > 0) {
            EmployeeDepartmentHistory::query()->create([
                'employee_profile_id' => $afterProfile->id,
                'old_department_id' => $oldDepartmentId > 0 ? $oldDepartmentId : null,
                'new_department_id' => $newDepartmentId,
                'changed_at' => now(),
                'changed_by' => $changedBy,
                'reason' => 'Department changed from user update',
            ]);
        }

        $oldPositionId = (int) ($beforeProfile?->position_id ?? 0);
        $newPositionId = (int) ($afterProfile->position_id ?? 0);
        if ($oldPositionId !== $newPositionId && $newPositionId > 0) {
            EmployeePositionHistory::query()->create([
                'employee_profile_id' => $afterProfile->id,
                'old_position_id' => $oldPositionId > 0 ? $oldPositionId : null,
                'new_position_id' => $newPositionId,
                'changed_at' => now(),
                'changed_by' => $changedBy,
                'reason' => 'Position changed from user update',
            ]);
        }

        $oldStatus = (string) ($beforeProfile?->employment_status ?? '');
        $newStatus = (string) ($afterProfile->employment_status ?? '');
        if ($oldStatus !== $newStatus && $newStatus !== '') {
            EmployeeStatusLog::query()->create([
                'employee_profile_id' => $afterProfile->id,
                'old_status' => $oldStatus ?: null,
                'new_status' => $newStatus,
                'reason' => 'Employment status changed from user update',
                'changed_by' => $changedBy,
            ]);
        }
    }
}
