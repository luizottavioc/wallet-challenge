<?php

namespace App\Domain\Exception;

use Exception;

class AbstractException extends Exception
{
    public const string CUSTOM_MESSAGE = 'unexpected_error';

    public function __construct()
    {
        parent::__construct(self::CUSTOM_MESSAGE);
    }
}