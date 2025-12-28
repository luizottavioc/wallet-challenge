<?php

declare(strict_types=1);

namespace App\Domain\Event;

final readonly class TransferCompletedEvent
{
    public function __construct(public string $transactionId) {}
}