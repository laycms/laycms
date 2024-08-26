<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;

class IndexController extends Controller
{
    public function index(): Renderable
    {
        return view('admin::index');
    }
}
