<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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
        //
    }
}
