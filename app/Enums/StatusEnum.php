<?php

declare(strict_types=1);

namespace App\Enums;

use Juling\Foundation\Contracts\EnumMethodInterface;
use Juling\Foundation\Enums\EnumMethods;

enum StatusEnum: int implements EnumMethodInterface
{
    use EnumMethods;
}
