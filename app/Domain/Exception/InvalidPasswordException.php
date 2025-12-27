<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class InvalidPasswordException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'invalid_password';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}