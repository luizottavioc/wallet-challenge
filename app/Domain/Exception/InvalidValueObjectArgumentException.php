<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class InvalidValueObjectArgumentException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'invalid_value_object_argument';

    public function getCustomMessage(): ?string
    {
        return self::CUSTOM_MESSAGE;
    }
}