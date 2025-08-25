<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use App\Mail\Transport\MicrosoftGraphTransport;

class MicrosoftGraphServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the Microsoft Graph mailer transport
        Mail::extend('microsoft-graph', function (array $config) {
            return new MicrosoftGraphTransport($config);
        });
    }
}
