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

use App\Domain\Exception\AbstractException;
use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Trait\HttpResponseTrait;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function Hyperf\Config\config;

class AppExceptionHandler extends ExceptionHandler
{
    use HttpResponseTrait;

    public function __construct(
        protected StdoutLoggerInterface $logger
    ) {}

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->logger->error(
            sprintf(
                '%s[%s] in %s',
                $throwable->getMessage(),
                $throwable->getLine(),
                $throwable->getFile()
            )
        );

        $this->logger->error($throwable->getTraceAsString());

        [$message, $statusCode] = $this->resolveMessageAndStatusCodeByThrowable($throwable);

        return $this->responseError($statusCode, $message, [], $response);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

    /**
     * @return array{message: string, statusCode: HttpCodesEnum}
     */
    private function resolveMessageAndStatusCodeByThrowable(Throwable $throwable): array
    {
        $errorsMap = config('errors', []);
        $throwableClass = get_class($throwable);

        $mappedThrowable = $errorsMap[$throwableClass] ?? null;
        if (!$throwable instanceof AbstractException || !$mappedThrowable instanceof HttpCodesEnum) {
            return [
                AbstractException::CUSTOM_MESSAGE,
                HttpCodesEnum::INTERNAL_SERVER_ERROR
            ];
        }

        return [
            $throwable->getMessage(),
            $mappedThrowable
        ];
    }
}
