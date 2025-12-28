<?php

declare(strict_types=1);

namespace App\Application\Exception;

use App\Domain\Exception\AbstractException;

class UnauthorizedTransferException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'unauthorized_transfer';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}