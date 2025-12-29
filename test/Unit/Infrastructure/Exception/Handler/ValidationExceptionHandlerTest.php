<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Exception\Handler;

use App\Infrastructure\Enum\HttpCodesEnum;
use App\Infrastructure\Exception\Handler\ValidationExceptionHandler;
use Hyperf\Contract\ValidatorInterface;
use Hyperf\Support\MessageBag;
use Hyperf\Validation\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Swow\Psr7\Message\ResponsePlusInterface;

final class ValidationExceptionHandlerTest extends TestCase
{
    private ValidationExceptionHandler $handler;
    private ResponsePlusInterface|MockObject $response;

    protected function setUp(): void
    {
        $this->handler = new ValidationExceptionHandler();
        $this->response = $this->createMock(ResponsePlusInterface::class);
    }

    public function testHandleWithValidationException(): void
    {
        $errors = ['field1' => ['Required'], 'field2' => ['Invalid format']];
        $throwable = $this->createValidationException($errors);

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
            ->with(HttpCodesEnum::UNPROCESSABLE_ENTITY->value)
            ->willReturnSelf();

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithNonValidationException(): void
    {
        $throwable = new RuntimeException('Generic error');

        $this->response
            ->expects($this->never())
            ->method('withHeader');

        $this->response
            ->expects($this->never())
            ->method('withBody');

        $this->response
            ->expects($this->never())
            ->method('withStatus');

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertTrue($result);
    }

    public function testIsValidWithValidationException(): void
    {
        $errors = ['field1' => ['Required']];
        $throwable = $this->createValidationException($errors);

        $result = $this->handler->isValid($throwable);

        $this->assertTrue($result);
    }

    public function testIsValidWithNonValidationException(): void
    {
        $throwable = new RuntimeException('Generic error');

        $result = $this->handler->isValid($throwable);

        $this->assertFalse($result);
    }

    public function testHandleWithEmptyErrors(): void
    {
        $errors = [];
        $throwable = $this->createValidationException($errors);

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
            ->with(HttpCodesEnum::UNPROCESSABLE_ENTITY->value)
            ->willReturnSelf();

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithSingleError(): void
    {
        $errors = ['email' => ['Invalid email format']];
        $throwable = $this->createValidationException($errors);

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
            ->with(HttpCodesEnum::UNPROCESSABLE_ENTITY->value)
            ->willReturnSelf();

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithMultipleErrorsPerField(): void
    {
        $errors = [
            'email' => ['Required', 'Invalid format'],
            'password' => ['Required', 'Too short', 'Missing uppercase'],
            'name' => ['Required']
        ];
        $throwable = $this->createValidationException($errors);

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
            ->with(HttpCodesEnum::UNPROCESSABLE_ENTITY->value)
            ->willReturnSelf();

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithSpecialCharactersInErrors(): void
    {
        $errors = ['name' => ['O campo nome é obrigatório', 'O nome deve ter pelo menos 3 caracteres']];
        $throwable = $this->createValidationException($errors);

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
            ->with(HttpCodesEnum::UNPROCESSABLE_ENTITY->value)
            ->willReturnSelf();

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithNumericFieldNames(): void
    {
        $errors = ['0' => ['Required'], '1' => ['Invalid format']];
        $throwable = $this->createValidationException($errors);

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
            ->with(HttpCodesEnum::UNPROCESSABLE_ENTITY->value)
            ->willReturnSelf();

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandleWithHttpException(): void
    {
        $throwable = new RuntimeException('Generic error');

        $this->response
            ->expects($this->never())
            ->method('withHeader');

        $this->response
            ->expects($this->never())
            ->method('withBody');

        $this->response
            ->expects($this->never())
            ->method('withStatus');

        $result = $this->handler->handle($throwable, $this->response);

        $this->assertTrue($result);
    }

    private function createValidationException(array $errors): ValidationException
    {
        $validator = $this->createMock(ValidatorInterface::class);

        $validator->method('errors')->willReturn(new MessageBag($errors));

        return new ValidationException($validator);
    }
}
