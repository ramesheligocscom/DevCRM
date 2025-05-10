<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use App\Models\UserLoginLog;
use Stevebauman\Location\Facades\Location;

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
        # Listen to login event
        Event::listen(Login::class, function ($event) {
            $ip = getIpAddress();
            $location = Location::get($ip);

            UserLoginLog::create([
                'user_id'    => $event->user->uuid,
                'ip_address' => $ip,
                'user_agent' => request()->header('User-Agent'),
                'event'      => 'login',
                'logged_at'  => now(),
                'country'    => $location?->countryName ?? '',
                'state'      => $location?->regionName ?? '',
                'city'       => $location?->cityName ?? '',
            ]);
        });

        # Listen to failed login event
        Event::listen(Failed::class, function ($event) {
            $ip = getIpAddress();
            $location = Location::get($ip);

            UserLoginLog::create([
                'user_id'    => null,
                'ip_address' => $ip,
                'user_agent' => request()->header('User-Agent'),
                'event'      => 'login',
                'logged_at'  => now(),
                'country'    => $location?->countryName ?? '',
                'state'      => $location?->regionName ?? '',
                'city'       => $location?->cityName ?? '',
            ]);
        });

        # Listen to logout event
        Event::listen(Logout::class, function ($event) {
            $ip = request()->ip();
            $location = Location::get($ip);
            UserLoginLog::create([
                'user_id'    => $event->user->uuid,
                'ip_address' => $ip,
                'user_agent' => request()->header('User-Agent'),
                'event'      => 'logout',
                'logged_at'  => now(),
                'country'    => $location?->countryName ?? '',
                'state'      => $location?->regionName ?? '',
                'city'       => $location?->cityName ?? '',
            ]);
        });
    }

    function module_exists(string $moduleName): bool
    {
        return \Module::has($moduleName) && \Module::isEnabled($moduleName);
    }
}
