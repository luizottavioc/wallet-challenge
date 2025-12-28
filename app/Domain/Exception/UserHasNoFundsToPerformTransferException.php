<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class UserHasNoFundsToPerformTransferException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'user_has_no_funds_to_perform_transfer';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}