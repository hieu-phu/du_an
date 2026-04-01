<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserService extends BaseService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    /**
     * Lấy danh sách nhân sự có phân trang + filter.
     */
    public function getListPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getListPaginated($filters, $perPage);
    }

    /**
     * Tạo nhân sự mới.
     */
    public function createUser(array $validatedData, ?UploadedFile $avatarFile = null): User
    {
        return $this->handleTransaction(function () use ($validatedData, $avatarFile) {
            $avatarPath = null;
            $thumbnailPath = null;

            try {
                if ($avatarFile) {
                    $avatarPath = $this->handleAvatarUpload($avatarFile);
                    $thumbnailPath = $this->generateThumbnail($avatarFile);
                }

                $user = $this->userRepository->createUser([
                    'name'       => $validatedData['name'],
                    'username'   => $validatedData['email'],
                    'email'      => $validatedData['email'],
                    'phone'      => $validatedData['phone'],
                    'password'   => Hash::make($validatedData['password']),
                    'address'    => $validatedData['address'] ?? null,
                    'status'     => $validatedData['status'],
                    'is_employee' => 1,
                    'avatar'     => $avatarPath,
                    'thumbnail'  => $thumbnailPath,
                    'creater_id' => $this->user()?->id,
                    'slug'       => Str::slug($validatedData['name']) . '-' . Str::random(6),
                ]);

                return $user;
            } catch (\Exception $e) {
                $this->cleanupAvatar($avatarPath, $thumbnailPath);
                throw $e;
            }
        });
    }

    /**
     * Cập nhật thông tin nhân sự.
     */
    public function updateUser(User $user, array $validatedData, ?UploadedFile $avatarFile = null): bool
    {
        return $this->handleTransaction(function () use ($user, $validatedData, $avatarFile) {
            $avatarPath = $user->avatar;
            $thumbnailPath = $user->thumbnail;
            $oldAvatarPath = null;
            $oldThumbnailPath = null;

            try {
                if ($avatarFile) {
                    $oldAvatarPath = $avatarPath;
                    $oldThumbnailPath = $thumbnailPath;

                    $avatarPath = $this->handleAvatarUpload($avatarFile);
                    $thumbnailPath = $this->generateThumbnail($avatarFile);
                }

                $userData = [
                    'name'      => $validatedData['name'],
                    'email'     => $validatedData['email'],
                    'phone'     => $validatedData['phone'],
                    'address'   => $validatedData['address'] ?? null,
                    'status'    => $validatedData['status'],
                    'avatar'    => $avatarPath,
                    'thumbnail' => $thumbnailPath,
                ];

                if (!empty($validatedData['password'])) {
                    $userData['password'] = Hash::make($validatedData['password']);
                }

                $result = $this->userRepository->updateUser($user, $userData);

                // Xóa ảnh cũ sau khi update thành công
                if ($avatarFile) {
                    $this->cleanupAvatar($oldAvatarPath, $oldThumbnailPath);
                }

                // Gửi notification
                $this->sendUpdateNotification($user);

                return $result;
            } catch (\Exception $e) {
                // Nếu lỗi, xóa ảnh mới vừa upload
                if ($avatarFile) {
                    $this->cleanupAvatar($avatarPath, $thumbnailPath);
                }
                throw $e;
            }
        });
    }

    /**
     * Chuyển đổi trạng thái nhân sự (active <-> inactive).
     */
    public function toggleStatus(User $user): bool
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';

        return $this->userRepository->updateStatus($user, $newStatus);
    }

    /**
     * Upload avatar và trả về path.
     */
    private function handleAvatarUpload(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    /**
     * Tạo thumbnail từ ảnh gốc.
     */
    private function generateThumbnail(UploadedFile $file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());
        $image->cover(200, 200);

        $thumbnailPath = 'avatars/thumbnails/' . time() . '_' . $file->getClientOriginalName();
        Storage::disk('public')->put($thumbnailPath, (string) $image->encode());

        return $thumbnailPath;
    }

    /**
     * Xóa avatar và thumbnail khỏi storage.
     */
    private function cleanupAvatar(?string $avatarPath, ?string $thumbnailPath): void
    {
        if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
            Storage::disk('public')->delete($avatarPath);
        }
        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }
    }

    /**
     * Gửi notification sau khi cập nhật nhân sự.
     */
    private function sendUpdateNotification(User $user): void
    {
        $currentUser = $this->user();
        if (!$currentUser) {
            return;
        }

        try {
            $notificationService = app(NotificationService::class);
            $notificationService->create(
                userId: $currentUser->id,
                title: 'Cập nhật thông tin nhân sự',
                message: 'Thông tin của bạn đã được cập nhật bởi ' . $currentUser->name,
                data: [
                    'category' => 'user',
                    'user' => [
                        'name'   => $currentUser->name,
                        'avatar' => $currentUser->thumbnail ?? null,
                    ],
                ],
                urlLink: '/profile',
                category: 'user'
            );
        } catch (\Exception $e) {
            // Notification failure không nên break flow chính
            \Illuminate\Support\Facades\Log::warning('Failed to send user update notification: ' . $e->getMessage());
        }
    }
}
