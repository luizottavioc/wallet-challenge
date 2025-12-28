<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Enum\UserTypeEnum;

final readonly class AccessTokenDto
{
    public function __construct(
        private string $token,
        private string $userId,
        private UserTypeEnum $userType,
        private int $createdAt,
        private int $expiresAt,
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserType(): UserTypeEnum
    {
        return $this->userType;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }

    public function getRemainingTime(): int
    {
        return $this->expiresAt - time();
    }
}