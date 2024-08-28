<?php

declare(strict_types=1);

namespace App\Modules\Admin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AdminModuleProvider extends ServiceProvider
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
        if (is_dir(__DIR__ . '/Routes')) {
            $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        }
        if (is_dir(__DIR__ . '/Views')) {
            $module = basename(__DIR__);
            $this->loadViewsFrom(__DIR__ . '/Views', Str::snake($module));
        }
    }
}
