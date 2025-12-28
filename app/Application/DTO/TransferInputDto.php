<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class TransferInputDto
{
    public function __construct(
        public string $payerId,
        public string $payeeId,
        public int $amount
    ) {}
}