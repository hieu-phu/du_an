<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy danh sách thông báo cho user hiện tại (login context).
     */
    public function getForUserLogin(?string $subdomain = null, bool $unreadOnly = false, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->query()
            ->forUserLogin()
            ->orderBy('created_at', 'desc')
            ->where(function ($q) use ($subdomain) {
                $q->whereNull('subdomain')
                    ->orWhere('subdomain', '=', $subdomain);
            });

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->paginate($perPage);
    }

    /**
     * Lấy thông báo theo category cho user hiện tại.
     */
    public function getByCategory(string $category, ?string $subdomain = null, bool $unreadOnly = false, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->query()
            ->forUserLogin()
            ->byCategory($category)
            ->orderBy('created_at', 'desc')
            ->where(function ($q) use ($subdomain) {
                $q->whereNull('subdomain')
                    ->orWhere('subdomain', '=', $subdomain);
            });

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->paginate($perPage);
    }

    /**
     * Đếm số thông báo chưa đọc.
     */
    public function countUnread(?string $subdomain = null): int
    {
        return $this->query()
            ->forUserLogin()
            ->where(function ($q) use ($subdomain) {
                $q->whereNull('subdomain')
                    ->orWhere('subdomain', '=', $subdomain);
            })
            ->unread()
            ->count();
    }

    /**
     * Đếm thông báo chưa đọc theo từng category.
     */
    public function countUnreadByCategory(?string $subdomain = null): array
    {
        $notifications = $this->query()
            ->forUserLogin()
            ->where(function ($q) use ($subdomain) {
                $q->whereNull('subdomain')
                    ->orWhere('subdomain', '=', $subdomain);
            })
            ->unread()
            ->get();

        $counts = [
            'all' => $notifications->count(),
            'general' => 0,
        ];

        foreach ($notifications as $notification) {
            $category = $notification->category ?? 'general';
            $counts[$category] = (int) ($counts[$category] ?? 0) + 1;

            $path = $this->resolveNotificationPath($notification);

            if ($path) {
                $counts['path:' . $path] = (int) ($counts['path:' . $path] ?? 0) + 1;
            }
        }

        return $counts;
    }

    private function resolveNotificationPath(Notification $notification): ?string
    {
        $url = $notification->url_link ?: data_get($notification->data, 'action_url');

        if (!$url) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return null;
        }

        return $path;
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc.
     */
    public function markAllRead(): int
    {
        return $this->query()
            ->forUserLogin()
            ->unread()
            ->update(['read_at' => now('Asia/Ho_Chi_Minh')]);
    }

    /**
     * Xoá thông báo cũ hơn N ngày.
     */
    public function deleteOlderThan(int $days = 90): int
    {
        return $this->query()
            ->where('created_at', '<', now('Asia/Ho_Chi_Minh')->subDays($days))
            ->delete();
    }
}
