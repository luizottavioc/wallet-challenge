<?php

declare(strict_types=1);

namespace App\Infrastructure\Trait;

use App\Domain\Enum\UserTypeEnum;
use Hyperf\Context\Context;

trait RequestContextTrait
{
    public function getContextUserId(): ?string
    {
        return Context::get('auth.user_id');
    }

    public function getContextUserType(): ?UserTypeEnum
    {
        return Context::get('auth.user_type');
    }

    public function getContextUserToken(): ?string
    {
        return Context::get('auth.token');
    }
}