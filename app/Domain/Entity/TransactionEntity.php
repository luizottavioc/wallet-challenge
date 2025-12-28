<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;

final readonly class TransactionEntity
{
    public function __construct(
        private Identifier $id,
        private UserEntity $payer,
        private UserEntity $payee,
        private Money $amount,
        private PrecisionTimestamp $processedAt
    ) {}

    public function getId(): Identifier
    {
        return $this->id;
    }

    public function getPayer(): UserEntity
    {
        return $this->payer;
    }

    public function getPayee(): UserEntity
    {
        return $this->payee;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getProcessedAt(): PrecisionTimestamp
    {
        return $this->processedAt;
    }
}