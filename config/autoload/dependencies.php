<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Application\Contract\AuthenticatorInterface;
use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Contract\Service\PasswordHasherInterface;
use App\Infrastructure\Adapter\AuthenticatorAdapterJWT;
use App\Infrastructure\Adapter\PasswordHasherAdapterBcrypt;
use App\Infrastructure\Repository\UserRepositoryEloquent;

return [
    UserRepositoryInterface::class => UserRepositoryEloquent::class,
    AuthenticatorInterface::class => AuthenticatorAdapterJWT::class,
    PasswordHasherInterface::class => PasswordHasherAdapterBcrypt::class,
];
