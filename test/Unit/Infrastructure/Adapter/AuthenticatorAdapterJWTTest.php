<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Adapter;

use App\Domain\Entity\UserEntity;
use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Adapter\AuthenticatorAdapterJWT;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;
use function Hyperf\Config\config;

final class AuthenticatorAdapterJWTTest extends TestCase
{
    private AuthenticatorAdapterJWT $authenticator;

    protected function setUp(): void
    {
        $this->authenticator = new AuthenticatorAdapterJWT();
    }

    public function testGenerateTokenSuccessfully(): void
    {
        $userId = 1;
        $email = 'test@example.com';
        $userType = UserTypeEnum::DEFAULT;
        $now = time();

        $user = new UserEntity(
            id: $userId,
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $result = $this->authenticator->generateToken($user);

        $this->assertEquals($userId, $result->getUserId());
        $this->assertEquals($userType, $result->getUserType());
        $this->assertEquals($now, $result->getCreatedAt());
        $this->assertEquals($now + 3600, $result->getExpiresAt());
        $this->assertIsString($result->getToken());
        $this->assertNotEmpty($result->getToken());
    }

    public function testGenerateTokenWithShopkeeperUser(): void
    {
        $userId = 2;
        $email = 'shopkeeper@example.com';
        $userType = UserTypeEnum::SHOPKEEPER;
        $now = time();

        $user = new UserEntity(
            id: $userId,
            name: 'Shopkeeper User',
            email: $email,
            cpf: null,
            cnpj: '12345678901234',
            password: 'hashed_password',
            type: $userType
        );

        $result = $this->authenticator->generateToken($user);

        $this->assertEquals($userId, $result->getUserId());
        $this->assertEquals($userType, $result->getUserType());
        $this->assertEquals($now, $result->getCreatedAt());
        $this->assertEquals($now + 3600, $result->getExpiresAt());
        $this->assertIsString($result->getToken());
        $this->assertNotEmpty($result->getToken());
    }

    public function testTokenIsValidWithValidToken(): void
    {
        $userId = 1;
        $email = 'test@example.com';
        $userType = UserTypeEnum::DEFAULT;

        $user = new UserEntity(
            id: $userId,
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $tokenDto = $this->authenticator->generateToken($user);

        $result = $this->authenticator->tokenIsValid($tokenDto->getToken());

        $this->assertTrue($result);
    }

    public function testTokenIsInvalidWithExpiredToken(): void
    {
        $userId = 1;
        $email = 'test@example.com';
        $userType = UserTypeEnum::DEFAULT;
        $pastTime = time() - 7200;

        $tokenPayload = [
            'sub' => $userId,
            'email' => $email,
            'type' => $userType->value,
            'iat' => $pastTime - 3600,
            'exp' => $pastTime,
        ];


        $expiredToken = JWT::encode(
            $tokenPayload,
            config('auth.jwt_secret_key'),
            'HS256'
        );

        $result = $this->authenticator->tokenIsValid($expiredToken);

        $this->assertFalse($result);
    }

    public function testTokenIsInvalidWithAnotherSecretKey(): void
    {
        $userId = 1;
        $email = 'test@example.com';
        $userType = UserTypeEnum::DEFAULT;
        $pastTime = time() - 7200;

        $tokenPayload = [
            'sub' => $userId,
            'email' => $email,
            'type' => $userType->value,
            'iat' => $pastTime - 3600,
            'exp' => $pastTime,
        ];


        $expiredToken = JWT::encode(
            $tokenPayload,
            'another-string-secret-at-least-256-bits-long',
            'HS256'
        );

        $result = $this->authenticator->tokenIsValid($expiredToken);

        $this->assertFalse($result);
    }

    public function testDecodeTokenSuccessfully(): void
    {
        $userId = 1;
        $email = 'test@example.com';
        $userType = UserTypeEnum::DEFAULT;
        $now = time();

        $user = new UserEntity(
            id: $userId,
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $tokenDto = $this->authenticator->generateToken($user);

        $result = $this->authenticator->decodeToken($tokenDto->getToken());

        $this->assertEquals($tokenDto->getToken(), $result->getToken());
        $this->assertEquals($userId, $result->getUserId());
        $this->assertEquals($userType, $result->getUserType());
        $this->assertEquals($now, $result->getCreatedAt());
        $this->assertEquals($now + 3600, $result->getExpiresAt());
    }

    public function testDecodeTokenWithShopkeeperUser(): void
    {
        $userId = 2;
        $email = 'shopkeeper@example.com';
        $userType = UserTypeEnum::SHOPKEEPER;
        $now = time();

        $user = new UserEntity(
            id: $userId,
            name: 'Shopkeeper User',
            email: $email,
            cpf: null,
            cnpj: '12345678901234',
            password: 'hashed_password',
            type: $userType
        );

        $tokenDto = $this->authenticator->generateToken($user);

        $result = $this->authenticator->decodeToken($tokenDto->getToken());

        $this->assertEquals($tokenDto->getToken(), $result->getToken());
        $this->assertEquals($userId, $result->getUserId());
        $this->assertEquals($userType, $result->getUserType());
        $this->assertEquals($now, $result->getCreatedAt());
        $this->assertEquals($now + 3600, $result->getExpiresAt());
    }

    public function testGetRemainingTime(): void
    {
        $userId = 1;
        $email = 'test@example.com';
        $userType = UserTypeEnum::DEFAULT;

        $user = new UserEntity(
            id: $userId,
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $tokenDto = $this->authenticator->generateToken($user);

        $remainingTime = $tokenDto->getRemainingTime();

        $this->assertIsInt($remainingTime);
        $this->assertGreaterThan(3500, $remainingTime);
        $this->assertLessThanOrEqual(3600, $remainingTime);
    }
}
