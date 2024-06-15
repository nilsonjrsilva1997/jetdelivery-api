<?php

namespace App\Providers;

use App\Services\GeocodingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registre o GeocodingService com o apiKey necessário
        $this->app->singleton(GeocodingService::class, function ($app) {
            return new GeocodingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
