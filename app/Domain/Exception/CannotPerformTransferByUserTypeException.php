<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class CannotPerformTransferByUserTypeException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'cannot_perform_transfer_by_user_type';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}