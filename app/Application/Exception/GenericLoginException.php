<?php

declare(strict_types=1);

namespace App\Application\Exception;

use App\Domain\Exception\AbstractException;

class GenericLoginException extends AbstractException
{
    public const string CUSTOM_MESSAGE = 'authentication_failed';
}