<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SubsidiaryRepository;
use Juling\Foundation\Contracts\ServiceInterface;
use Juling\Foundation\Services\CommonService;

class SubsidiaryService extends CommonService implements ServiceInterface
{
    public function getRepository(): SubsidiaryRepository
    {
        return SubsidiaryRepository::getInstance();
    }
}
