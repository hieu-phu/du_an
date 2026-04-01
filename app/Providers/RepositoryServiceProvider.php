<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\UserRepository;
use App\Repositories\NotificationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Danh sách tất cả Repository cần đăng ký.
     * Khi thêm Repository mới, chỉ cần thêm class vào mảng này.
     */
    protected array $repositories = [
        UserRepository::class,
        NotificationRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositories as $repository) {
            $this->app->singleton($repository);
        }
    }
}
