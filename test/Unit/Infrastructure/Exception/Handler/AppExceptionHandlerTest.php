<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Exception\Handler;

use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Exception\Handler\AppExceptionHandler;
use Error;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpMessage\Exception\HttpException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class AppExceptionHandlerTest extends TestCase
{
    private StdoutLoggerInterface|MockObject $logger;
    private AppExceptionHandler $handler;
    private ResponseInterface|MockObject $response;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(StdoutLoggerInterface::class);
        $this->handler = new AppExceptionHandler($this->logger);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testHandleWithHttpException(): void
    {
        $statusCode = HttpCodesEnum::BAD_REQUEST;
        $errorMessage = 'Bad request error';
        $throwable = new HttpException($statusCode->value, $errorMessage);

        $this->logger
            ->expects($this->exactly(2))
            ->method('error')
            ->willReturnOnConsecutiveCalls(
                [$this->stringContains($errorMessage)],
                [$this->stringContains('AppExceptionHandlerTest')]
            );

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withBody')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with($statusCode->value)
            ->willReturnSelf();

        $this->handler->handle($throwable, $this->response);
    }

    public function testHandleWithGenericException(): void
    {
        $errorMessage = 'Generic error';
        $throwable = new RuntimeException($errorMessage);

        $this->logger
            ->expects($this->exactly(2))
            ->method('error')
            ->willReturnOnConsecutiveCalls(
                [$this->stringContains($errorMessage)],
                [$this->stringContains('AppExceptionHandlerTest')]
            );

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withBody')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        $this->handler->handle($throwable, $this->response);
    }

    public function testIsValidAlwaysReturnsTrue(): void
    {
        $throwable = new RuntimeException('Test error');

        $result = $this->handler->isValid($throwable);

        $this->assertTrue($result);
    }

    public function testHandleWithEmptyMessage(): void
    {
        $throwable = new RuntimeException('');

        $this->logger
            ->expects($this->exactly(2))
            ->method('error')
            ->willReturnOnConsecutiveCalls(
                [$this->stringContains('')],
                [$this->stringContains('AppExceptionHandlerTest')]
            );

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withBody')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        $this->handler->handle($throwable, $this->response);
    }

    public function testHandleWithFatalError(): void
    {
        $errorMessage = 'Fatal error occurred';
        $throwable = new Error($errorMessage);

        $this->logger
            ->expects($this->exactly(2))
            ->method('error')
            ->willReturnOnConsecutiveCalls(
                [$this->stringContains($errorMessage)],
                [$this->stringContains('AppExceptionHandlerTest')]
            );

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withBody')
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        $this->handler->handle($throwable, $this->response);
    }
}
