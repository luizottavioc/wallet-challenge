<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\DTO;

use App\Application\DTO\AccessTokenDto;
use App\Domain\Enum\UserTypeEnum;
use PHPUnit\Framework\TestCase;

final class AccessTokenDtoTest extends TestCase
{
    public function testAccessTokenDtoCreation(): void
    {
        $token = 'jwt.token.string';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;
        $createdAt = time();
        $expiresAt = $createdAt + 3600;

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: $createdAt,
            expiresAt: $expiresAt
        );

        $this->assertEquals($token, $accessTokenDto->getToken());
        $this->assertEquals($userId, $accessTokenDto->getUserId());
        $this->assertEquals($userType, $accessTokenDto->getUserType());
        $this->assertEquals($createdAt, $accessTokenDto->getCreatedAt());
        $this->assertEquals($expiresAt, $accessTokenDto->getExpiresAt());
    }

    public function testGetRemainingTimeWithValidToken(): void
    {
        $token = 'jwt.token.string';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;
        $createdAt = time();
        $expiresAt = $createdAt + 3600;

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: $createdAt,
            expiresAt: $expiresAt
        );

        $remainingTime = $accessTokenDto->getRemainingTime();

        $this->assertIsInt($remainingTime);
        $this->assertGreaterThan(3500, $remainingTime);
        $this->assertLessThanOrEqual(3600, $remainingTime);
    }

    public function testGetRemainingTimeWithExpiredToken(): void
    {
        $token = 'jwt.token.string';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;
        $createdAt = time() - 7200;
        $expiresAt = $createdAt + 3600;

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: $createdAt,
            expiresAt: $expiresAt
        );

        $remainingTime = $accessTokenDto->getRemainingTime();

        $this->assertIsInt($remainingTime);
        $this->assertLessThan(0, $remainingTime);
    }

    public function testGetRemainingTimeWithTokenAboutToExpire(): void
    {
        $token = 'jwt.token.string';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;
        $createdAt = time() - 3595;
        $expiresAt = $createdAt + 3600;

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: $createdAt,
            expiresAt: $expiresAt
        );

        $remainingTime = $accessTokenDto->getRemainingTime();

        $this->assertIsInt($remainingTime);
        $this->assertGreaterThan(0, $remainingTime);
        $this->assertLessThan(10, $remainingTime);
    }

    public function testAccessTokenDtoWithShopkeeperUser(): void
    {
        $token = 'jwt.token.string';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::SHOPKEEPER;
        $createdAt = time();
        $expiresAt = $createdAt + 3600;

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: $createdAt,
            expiresAt: $expiresAt
        );

        $this->assertEquals($token, $accessTokenDto->getToken());
        $this->assertEquals($userId, $accessTokenDto->getUserId());
        $this->assertEquals(UserTypeEnum::SHOPKEEPER, $accessTokenDto->getUserType());
        $this->assertEquals($createdAt, $accessTokenDto->getCreatedAt());
        $this->assertEquals($expiresAt, $accessTokenDto->getExpiresAt());
    }
}
