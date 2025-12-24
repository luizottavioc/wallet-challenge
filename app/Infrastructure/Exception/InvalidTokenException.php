<?php

namespace App\Infrastructure\Exception;

use App\Domain\Exception\AbstractException;

class InvalidTokenException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'invalid_token';
}