<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\IpCalculationService\IpCalculationInterface;
use App\Services\IpCalculationService\IpCalculationService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IpCalculationInterface::class, IpCalculationService::class);
    }

    public function boot(): void
    {
        //
    }
}
