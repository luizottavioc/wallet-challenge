<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;

readonly class PrecisionTimestamp
{
    public const string DATE_FORMAT = 'Y-m-d H:i:s.u';
    public const string UTC_ZONE = 'UTC';

    private DateTimeImmutable $value;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(?DateTimeImmutable $time = null)
    {
        $timezone = new DateTimeZone(self::UTC_ZONE);

        if ($time instanceof DateTimeImmutable) {
            $this->value = $time->setTimezone($timezone);
            return;
        }

        $this->value = new DateTimeImmutable('now', $timezone);
    }

    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }

    public function format(): string
    {
        return $this->value->format(self::DATE_FORMAT);
    }
}