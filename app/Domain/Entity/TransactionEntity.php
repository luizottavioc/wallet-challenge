<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\TransactionStatusEnum;

final readonly class TransactionEntity
{
    public function __construct(
        private string|int $id,
        private string|int $payerId,
        private string|int $payeeId,
        private int $amount,
        private TransactionStatusEnum $status,
        private int $processedAt
    ) {}

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getPayerId(): int|string
    {
        return $this->payerId;
    }

    public function getPayeeId(): int|string
    {
        return $this->payeeId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getStatus(): TransactionStatusEnum
    {
        return $this->status;
    }

    public function getProcessedAt(): int
    {
        return $this->processedAt;
    }
}