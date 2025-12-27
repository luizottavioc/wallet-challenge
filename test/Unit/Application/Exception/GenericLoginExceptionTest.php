<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\Exception;

use App\Application\Exception\GenericLoginException;
use App\Domain\Exception\InvalidPasswordException;
use PHPUnit\Framework\TestCase;

final class GenericLoginExceptionTest extends TestCase
{
    public function testGenericLoginExceptionCreation(): void
    {
        $exception = new GenericLoginException();

        $this->assertInstanceOf(GenericLoginException::class, $exception);
        $this->assertEquals('authentication_failed', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testGenericLoginExceptionWithPreviousException(): void
    {
        $previousException = new InvalidPasswordException();
        $exception = new GenericLoginException($previousException);

        $this->assertInstanceOf(GenericLoginException::class, $exception);
        $this->assertEquals('authentication_failed', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testGenericLoginExceptionWithGenericPreviousException(): void
    {
        $previousException = new \RuntimeException('Previous error');
        $exception = new GenericLoginException($previousException);

        $this->assertInstanceOf(GenericLoginException::class, $exception);
        $this->assertEquals('authentication_failed', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testGenericLoginExceptionConstantMessage(): void
    {
        $this->assertEquals('authentication_failed', GenericLoginException::CUSTOM_MESSAGE);
    }
}
