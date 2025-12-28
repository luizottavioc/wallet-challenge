<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class CannotSubtractAmountException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'cannot_subtract_amount_exception';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}