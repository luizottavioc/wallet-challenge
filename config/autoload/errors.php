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

use App\Application\Exception\GenericLoginException;
use App\Application\Exception\UnauthorizedTransferException;
use App\Domain\Exception\CannotPerformTransferByInsufficientFundsException;
use App\Domain\Exception\CannotPerformTransferByUserTypeException;
use App\Domain\Exception\CannotPerformTransferForItselfException;
use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Exception\InvalidTokenException;

return [
    InvalidTokenException::class => HttpCodesEnum::UNAUTHORIZED,
    GenericLoginException::class => HttpCodesEnum::UNPROCESSABLE_ENTITY,
    UnauthorizedTransferException::class => HttpCodesEnum::FORBIDDEN,
    CannotPerformTransferByUserTypeException::class => HttpCodesEnum::FORBIDDEN,
    CannotPerformTransferForItselfException::class => HttpCodesEnum::FORBIDDEN,
    CannotPerformTransferByInsufficientFundsException::class => HttpCodesEnum::UNPROCESSABLE_ENTITY,
];
