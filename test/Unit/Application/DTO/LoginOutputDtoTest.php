<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\DTO;

use App\Application\DTO\LoginOutputDto;
use PHPUnit\Framework\TestCase;

final class LoginOutputDtoTest extends TestCase
{
    public function testLoginOutputDtoCreation(): void
    {
        $token = 'jwt.token.string';
        $expiresAt = time() + 3600;

        $loginOutputDto = new LoginOutputDto($token, $expiresAt);

        $this->assertEquals($token, $loginOutputDto->token);
        $this->assertEquals($expiresAt, $loginOutputDto->expiresAt);
    }

    public function testLoginOutputDtoToArray(): void
    {
        $token = 'jwt.token.string';
        $expiresAt = time() + 3600;

        $loginOutputDto = new LoginOutputDto($token, $expiresAt);
        $result = $loginOutputDto->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('expiresAt', $result);
        $this->assertEquals($token, $result['token']);
        $this->assertEquals($expiresAt, $result['expiresAt']);
    }

    public function testLoginOutputDtoWithZeroExpiration(): void
    {
        $token = 'jwt.token.string';
        $expiresAt = 0;

        $loginOutputDto = new LoginOutputDto($token, $expiresAt);

        $this->assertEquals($token, $loginOutputDto->token);
        $this->assertEquals($expiresAt, $loginOutputDto->expiresAt);

        $result = $loginOutputDto->toArray();
        $this->assertEquals($token, $result['token']);
        $this->assertEquals($expiresAt, $result['expiresAt']);
    }

    public function testLoginOutputDtoWithPastExpiration(): void
    {
        $token = 'jwt.token.string';
        $expiresAt = time() - 3600;

        $loginOutputDto = new LoginOutputDto($token, $expiresAt);

        $this->assertEquals($token, $loginOutputDto->token);
        $this->assertEquals($expiresAt, $loginOutputDto->expiresAt);

        $result = $loginOutputDto->toArray();
        $this->assertEquals($token, $result['token']);
        $this->assertEquals($expiresAt, $result['expiresAt']);
    }

    public function testLoginOutputDtoToArrayStructure(): void
    {
        $token = 'jwt.token.string';
        $expiresAt = time() + 3600;

        $loginOutputDto = new LoginOutputDto($token, $expiresAt);
        $result = $loginOutputDto->toArray();

        $this->assertCount(2, $result);
        $this->assertArrayNotHasKey('extra_key', $result);
        $this->assertArrayNotHasKey(0, $result);
        $this->assertArrayNotHasKey(1, $result);
    }
}
