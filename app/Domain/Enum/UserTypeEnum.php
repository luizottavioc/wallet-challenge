<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum UserTypeEnum: string
{
    case DEFAULT = 'default';
    case SHOPKEEPER = 'shopkeeper';
}
