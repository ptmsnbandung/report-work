<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Services\TiketService::class, function ($app) {
            return new \App\Services\TiketService(
                $app->make(\App\Services\NotificationService::class)
            );
        });

        $this->app->bind(\App\Services\KronologisService::class, function ($app) {
            return new \App\Services\KronologisService(
                $app->make(\App\Services\NotificationService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer(['tiket.index', 'dashboard.index', 'reports.*'], function ($view) {
            $user = auth()->user();
            $view->with('unreadTicketNotifs', $user ? $user->getUnreadNotifCountsPerTiket() : []);
        });
    }
}
