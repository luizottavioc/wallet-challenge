<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class UserTypeCannotPerformTransferException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'user_type_cannot_perform_transfer';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}