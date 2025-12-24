<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\TransactionStatusEnum;

final class TransactionEntity
{
    public function __construct(
        private readonly string|int $id,
        private readonly string|int $payerId,
        private readonly string|int $payeeId,
        private readonly int $amount,
        private readonly TransactionStatusEnum $status,
        private readonly int $processedAt
    ) {}
}