<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Microsoft\Provider;

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
        // Configure Microsoft Socialite Provider
        Socialite::extend('microsoft', function ($app) {
            $config = $app['config']['services.microsoft'];
            
            // Use tenant-specific endpoint instead of common
            if (isset($config['tenant'])) {
                $config['auth_url'] = "https://login.microsoftonline.com/{$config['tenant']}/oauth2/v2.0/authorize";
                $config['token_url'] = "https://login.microsoftonline.com/{$config['tenant']}/oauth2/v2.0/token";
            }
            
            return Socialite::buildProvider(Provider::class, $config);
        });
    }
}
