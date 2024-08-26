<?php

declare(strict_types=1);

namespace App\Modules\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
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
        $this->loadRoutesFrom(__DIR__ . '/Routes/route.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'admin');
    }
}
