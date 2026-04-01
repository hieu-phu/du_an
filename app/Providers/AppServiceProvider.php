<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        Inertia::share([
            'auth' => function () {
                return [
                    'user' => auth()->user() ? [
                        'id' => auth()->user()->id,
                        'name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                        'avatar' => auth()->user()->avatar, // nếu có
                    ] : null,
                    'menuItems' => $this->getMenuItems(),
                ];
            },
        ]);
    }
    private function getMenuItems()
    {
        $menuItems = [];

        $hosting = request()->getHost();
        switch ($hosting) {
            default:
                $menuItems = $this->webMenuItems();
                break;
        }

        return $menuItems;
    }

    private function webMenuItems()
    {
        $menuItems = [
            [
                'icon' => 'GridIcon',
                'name' => 'Dashboard',
                'path' => '/dashboard',
            ],
        ];

        return [
            [
                'title' => 'Menu',
                'items' => $menuItems,
            ],
        ];
    }
}