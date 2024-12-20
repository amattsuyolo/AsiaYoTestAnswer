<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\OrderService;
use App\Services\Contracts\CurrencyConverterResolverInterface;
use App\Services\CurrencyConverterResolver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(CurrencyConverterResolverInterface::class, CurrencyConverterResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
