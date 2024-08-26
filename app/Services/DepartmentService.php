<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DepartmentRepository;
use Juling\Foundation\Contracts\ServiceInterface;
use Juling\Foundation\Services\CommonService;

class DepartmentService extends CommonService implements ServiceInterface
{
    public function getRepository(): DepartmentRepository
    {
        return DepartmentRepository::getInstance();
    }
}
