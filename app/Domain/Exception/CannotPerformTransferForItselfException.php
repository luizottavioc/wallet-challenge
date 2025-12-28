<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class CannotPerformTransferForItselfException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'cannot_perform_transfer_for_itself';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}