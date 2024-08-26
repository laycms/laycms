<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/', [\App\Modules\Admin\Controllers\IndexController::class, 'index']);
});
