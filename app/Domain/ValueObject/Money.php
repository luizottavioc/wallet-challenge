<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;

readonly class Money
{
    private int  $value;

    /**
     * @throws InvalidValueObjectArgumentException
     */
    public function __construct(int $amountInCents)
    {
        if ($amountInCents < 0) {
            throw new InvalidValueObjectArgumentException();
        }

        $this->value = $amountInCents;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equalsOrGreater(Money $amount): bool
    {
        return $this->value >= $amount->getValue();
    }

    /**
     * @throws CannotSubtractAmountException
     * @throws InvalidValueObjectArgumentException
     */
    public function subtractAmount(Money $amountToSubtract): self
    {
        if ($this->value < $amountToSubtract->getValue()) {
            throw new CannotSubtractAmountException();
        }

        return new self($this->value - $amountToSubtract->getValue());
    }

    /**
     * @throws InvalidValueObjectArgumentException
     */
    public function addAmount(Money $amountToAdd): self
    {
        return new self($this->value + $amountToAdd->getValue());
    }

    public function format(): string
    {
        return '$ ' . number_format($this->value / 100, 2, ',', '.');
    }
}