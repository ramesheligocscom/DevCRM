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
        # Common function to log events
        $logEvent = function ($userId, string $event, bool $success) {
            $request = request();
            $ip = $request->ip();

            # Replace localhost IP with a test IP (optional)
            if (in_array($ip, ['127.0.0.1', '::1'])) {
                $ip = '66.102.0.0'; # Sample Google IP
            }

            $location = Location::get($ip);

            UserLoginLog::create([
                'user_id'    => $userId,
                'ip_address' => $ip,
                'user_agent' => $request->header('User-Agent'),
                'event'      => $event,
                'success'    => $success,
                'logged_at'  => now(),
                'country'    => $location?->countryName ?? 'Unknown',
                'state'      => $location?->regionName ?? 'Unknown',
                'city'       => $location?->cityName ?? 'Unknown',
            ]);
        };

        # Listen to login event
        Event::listen(Login::class, fn($event) => $logEvent($event->user->uuid, 'login', true));

        # Listen to failed login event
        Event::listen(Failed::class, fn() => $logEvent(null, 'login', false));

        # Listen to logout event
        Event::listen(Logout::class, fn($event) => $logEvent($event->user->uuid, 'logout', true));
    }

    function module_exists(string $moduleName): bool
    {
        return \Module::has($moduleName) && \Module::isEnabled($moduleName);
    }
    
}
