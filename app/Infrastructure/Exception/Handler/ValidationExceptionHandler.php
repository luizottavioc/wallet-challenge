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

namespace App\Infrastructure\Exception\Handler;

use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Trait\HttpResponseTrait;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    use HttpResponseTrait;

    public function handle(Throwable $throwable, ResponsePlusInterface $response): true|ResponseInterface
    {
        if (!$throwable instanceof ValidationException) {
            return true;
        }

        $this->stopPropagation();
        return $this->responseError(
            HttpCodesEnum::UNPROCESSABLE_ENTITY,
            'validation_error',
            $throwable->validator->errors()->getMessages(),
            $response
        );
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
