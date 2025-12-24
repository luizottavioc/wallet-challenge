<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\UserTypeEnum;

final readonly class AuthEntity
{
    public function __construct(
        private string $originalToken,
        private string|int $userId,
        private UserTypeEnum $userType,
        private int $createdAt,
        private int $expiresAt,
    ) {}

    public function getOriginalToken(): string
    {
        return $this->originalToken;
    }

    public function getUserId(): int|string
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