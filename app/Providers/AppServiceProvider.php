<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\SwissPairingService::class);
        $this->app->singleton(\App\Services\StandingsService::class);
        $this->app->singleton(\App\Services\MarginCalculatorService::class);
        $this->app->singleton(\App\Services\InventoryService::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}
