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
            'user' => 0,
            'order' => 0,
            'system' => 0,
        ];

        foreach ($notifications as $notification) {
            $category = $notification->category ?? 'general';
            if (isset($counts[$category])) {
                $counts[$category]++;
            }
        }

        return $counts;
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
