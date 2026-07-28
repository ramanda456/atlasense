<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (empty(config('app.key'))) {
            config(['app.key' => env('APP_KEY', 'base64:glz0123a3r1IvhExYTJxqC7+uwwTZrIowKx+fLBSbc4=')]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $host = request()->getHost();
        if (!in_array($host, ['127.0.0.1', 'localhost'], true) && (config('app.env') === 'production' || request()->server('HTTP_X_FORWARDED_PROTO') === 'https')) {
            URL::forceScheme('https');
        }
    }
}
