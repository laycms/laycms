<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->loadRoutes();
        $this->loadProviders();
    }

    private function loadRoutes(): void
    {
        $apiRoutes = glob(app_path('API/Routes/*.php'));
        foreach ($apiRoutes as $route) {
            $this->loadRoutesFrom($route);
        }
    }

    private function loadProviders(): void
    {
        $providers = array_merge(
            glob(app_path('Bundles/*/*Provider.php')),
            glob(app_path('Modules/*/*Provider.php'))
        );

        foreach ($providers as $provider) {
            preg_match('/(app\/\w+\/\w+\/\w+Provider)/', $provider, $matches);
            if (isset($matches[1])) {
                $provider = str_replace('/', '\\', $matches[1]);
                $this->app->register(Str::studly($provider));
            }
        }
    }
}
