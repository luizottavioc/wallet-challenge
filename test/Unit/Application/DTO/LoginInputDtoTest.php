<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\DTO;

use App\Application\DTO\LoginInputDto;
use PHPUnit\Framework\TestCase;

final class LoginInputDtoTest extends TestCase
{
    public function testLoginInputDtoCreation(): void
    {
        $email = 'test@example.com';
        $password = 'password123';

        $loginInputDto = new LoginInputDto($email, $password);

        $this->assertEquals($email, $loginInputDto->email);
        $this->assertEquals($password, $loginInputDto->password);
    }

    public function testLoginInputDtoWithEmptyEmail(): void
    {
        $email = '';
        $password = 'password123';

        $loginInputDto = new LoginInputDto($email, $password);

        $this->assertEquals($email, $loginInputDto->email);
        $this->assertEquals($password, $loginInputDto->password);
    }

    public function testLoginInputDtoWithEmptyPassword(): void
    {
        $email = 'test@example.com';
        $password = '';

        $loginInputDto = new LoginInputDto($email, $password);

        $this->assertEquals($email, $loginInputDto->email);
        $this->assertEquals($password, $loginInputDto->password);
    }
}
