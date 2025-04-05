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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    function module_exists(string $moduleName): bool
    {
        return \Module::has($moduleName) && \Module::isEnabled($moduleName);
    }
    
}
