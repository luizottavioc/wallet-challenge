<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;

final class WalletEntity
{
    public function __construct(
        private readonly Identifier $id,
        private readonly UserEntity $user,
        private Money $amount,
        private PrecisionTimestamp $processedAt
    ) {}

    public function getId(): Identifier
    {
        return $this->id;
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getProcessedAt(): PrecisionTimestamp
    {
        return $this->processedAt;
    }

    public function hasFunds(Money $amount): bool
    {
        return $this->amount->equalsOrGreater($amount);
    }

    /**
     * @throws CannotSubtractAmountException
     * @throws InvalidValueObjectArgumentException
     */
    public function debit(Money $amount): self
    {
        return new self(
            new Identifier(),
            $this->user,
            $this->amount->subtractAmount($amount),
            new PrecisionTimestamp()
        );
    }

    /**
     * @throws InvalidValueObjectArgumentException
     */
    public function credit(Money $amount): self
    {
        return new self(
            new Identifier(),
            $this->user,
            $this->amount->addAmount($amount),
            new PrecisionTimestamp()
        );
    }
}