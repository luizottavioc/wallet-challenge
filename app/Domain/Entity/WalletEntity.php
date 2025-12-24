<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final class WalletEntity
{
    public function __construct(
        private readonly string|int $id,
        private readonly string|int $userId,
        private readonly int $amount,
        private readonly int $processedAt
    ) {}
}