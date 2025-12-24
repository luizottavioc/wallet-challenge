<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final readonly class WalletEntity
{
    public function __construct(
        private string|int $id,
        private string|int $userId,
        private int $amount,
        private int $processedAt
    ) {}

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getUserId(): int|string
    {
        return $this->userId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getProcessedAt(): int
    {
        return $this->processedAt;
    }
}