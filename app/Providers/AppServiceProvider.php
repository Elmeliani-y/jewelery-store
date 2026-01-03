<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Use Bootstrap 5 pagination views to match Dusty template styling
        Paginator::useBootstrapFive();
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        } else {
            // Force HTTP for local development to fix Vite asset URLs
            \URL::forceScheme('http');
        }

        // Add URL macro for prefixed routes
        \URL::macro('prefixed', function ($path = '') {
            $prefix = config('app.url_prefix', 'b75/n95uk');
            $path = ltrim($path, '/');
            return url($prefix . ($path ? '/' . $path : ''));
        });

        // Override route() helper to always include prefix
        \URL::macro('toRoute', function ($name, $parameters = [], $absolute = true) {
            $prefix = config('app.url_prefix', 'b75/n95uk');
            $route = app('url')->route($name, $parameters, $absolute);
            // If route doesn't start with prefix, add it
            if (!str_contains($route, '/' . $prefix . '/')) {
                $parsed = parse_url($route);
                $path = $parsed['path'] ?? '';
                $path = ltrim($path, '/');
                if ($path && $path !== $prefix) {
                    $route = str_replace($path, $prefix . '/' . $path, $route);
                }
            }
            return $route;
        });
    }
}
