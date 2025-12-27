<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;
use Throwable;

class AbstractException extends Exception
{
    public const string CUSTOM_MESSAGE = 'unexpected_error';

    public function __construct(?Throwable $throwable = null)
    {
        parent::__construct(self::CUSTOM_MESSAGE, 0, $throwable);
    }
}