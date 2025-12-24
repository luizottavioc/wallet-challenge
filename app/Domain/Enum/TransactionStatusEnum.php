<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
