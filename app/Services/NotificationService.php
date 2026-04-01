<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class NotificationService
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    /**
     * Tạo thông báo cho một user.
     */
    public function create(
        ?int $userId,
        string $title,
        string $message,
        ?array $data = null,
        ?string $urlLink = null,
        ?string $subdomain = null,
        ?string $category = 'general',
        ?int $createrId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Notification {
        $notification = $this->notificationRepository->create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'url_link' => $urlLink,
            'subdomain' => $subdomain,
            'category' => $category ?? 'general',
            'creater_id' => $createrId ?? auth()->id(),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);

        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    /**
     * Tạo thông báo cho nhiều user.
     */
    public function createForUsers(
        array $userIds,
        string $title,
        string $message,
        ?array $data = null,
        ?string $urlLink = null,
        ?string $subdomain = null,
        ?string $category = 'general',
        ?int $createrId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Collection {
        $notifications = collect();

        foreach ($userIds as $userId) {
            $notifications->push(
                $this->create($userId, $title, $message, $data, $urlLink, $subdomain, $category, $createrId, $referenceType, $referenceId)
            );
        }

        return $notifications;
    }

    /**
     * Lấy danh sách thông báo của user.
     */
    public function getUserNotifications(
        int $userId,
        ?bool $unreadOnly = false,
        int $perPage = 20
    ): LengthAwarePaginator {
        $subdomain = $this->getSubdomainFromRequest(request());

        return $this->notificationRepository->getForUserLogin($subdomain, $unreadOnly, $perPage);
    }

    /**
     * Lấy danh sách thông báo của user theo category.
     */
    public function getUserNotificationsByCategory(
        int $userId,
        string $category,
        ?bool $unreadOnly = false,
        int $perPage = 20
    ): LengthAwarePaginator {
        $subdomain = $this->getSubdomainFromRequest(request());

        return $this->notificationRepository->getByCategory($category, $subdomain, $unreadOnly, $perPage);
    }

    private function getSubdomainFromRequest(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) >= 3) {
            return $parts[0];
        }

        return 'main';
    }

    /**
     * Đánh dấu đã đọc.
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = $this->notificationRepository->getById($notificationId);

        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Đánh dấu tất cả là đã đọc.
     */
    public function markAllAsRead(int $userId): int
    {
        return $this->notificationRepository->markAllRead();
    }

    /**
     * Xóa thông báo.
     */
    public function delete(int $notificationId): bool
    {
        return $this->notificationRepository->delete($notificationId);
    }

    /**
     * Đếm số thông báo chưa đọc.
     */
    public function getUnreadCount(int $userId): int
    {
        $subdomain = $this->getSubdomainFromRequest(request());

        return $this->notificationRepository->countUnread($subdomain);
    }

    /**
     * Đếm số thông báo chưa đọc theo category.
     */
    public function getUnreadCountByCategory(int $userId): array
    {
        $subdomain = $this->getSubdomainFromRequest(request());

        return $this->notificationRepository->countUnreadByCategory($subdomain);
    }

    /**
     * Xóa thông báo cũ.
     */
    public function deleteOldNotifications(int $days = 90): int
    {
        return $this->notificationRepository->deleteOlderThan($days);
    }
}
