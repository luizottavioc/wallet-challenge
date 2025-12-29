<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Domain\ValueObject;

use App\Domain\ValueObject\PrecisionTimestamp;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PrecisionTimestampTest extends TestCase
{
    public function testPrecisionTimestampCreationWithDefaultTime(): void
    {
        $timestamp = new PrecisionTimestamp();

        $this->assertInstanceOf(DateTimeImmutable::class, $timestamp->getValue());
        $this->assertEquals('UTC', $timestamp->getValue()->getTimezone()->getName());
    }

    public function testPrecisionTimestampCreationWithProvidedTime(): void
    {
        $dateTime = new DateTimeImmutable('2023-12-25 10:30:45.123456');
        $timestamp = new PrecisionTimestamp($dateTime);

        $this->assertInstanceOf(DateTimeImmutable::class, $timestamp->getValue());
        $this->assertEquals('UTC', $timestamp->getValue()->getTimezone()->getName());
        $this->assertEquals('2023-12-25 10:30:45.123456', $timestamp->format());
    }

    public function testPrecisionTimestampWithDifferentTimezone(): void
    {
        $dateTime = new DateTimeImmutable('2023-12-25 10:30:45.123456', new \DateTimeZone('America/Sao_Paulo'));
        $timestamp = new PrecisionTimestamp($dateTime);

        $this->assertEquals('UTC', $timestamp->getValue()->getTimezone()->getName());
        $this->assertNotEquals('America/Sao_Paulo', $timestamp->getValue()->getTimezone()->getName());
    }

    public function testPrecisionTimestampFormat(): void
    {
        $dateTime = new DateTimeImmutable('2023-12-25 10:30:45.123456');
        $timestamp = new PrecisionTimestamp($dateTime);

        $formatted = $timestamp->format();

        $this->assertIsString($formatted);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{6}$/', $formatted);
    }

    public function testPrecisionTimestampFormatWithMicroseconds(): void
    {
        $dateTime = new DateTimeImmutable('2023-12-25 10:30:45.123456');
        $timestamp = new PrecisionTimestamp($dateTime);

        $formatted = $timestamp->format();

        $this->assertEquals('2023-12-25 10:30:45.123456', $formatted);
    }

    public function testPrecisionTimestampFormatWithoutMicroseconds(): void
    {
        $dateTime = new DateTimeImmutable('2023-12-25 10:30:45');
        $timestamp = new PrecisionTimestamp($dateTime);

        $formatted = $timestamp->format();

        $this->assertEquals('2023-12-25 10:30:45.000000', $formatted);
    }

    public function testPrecisionTimestampGetValue(): void
    {
        $dateTime = new DateTimeImmutable('2023-12-25 10:30:45.123456');
        $timestamp = new PrecisionTimestamp($dateTime);

        $value = $timestamp->getValue();

        $this->assertInstanceOf(DateTimeImmutable::class, $value);
        $this->assertEquals('UTC', $value->getTimezone()->getName());
    }

    public function testPrecisionTimestampWithMinDate(): void
    {
        $dateTime = new DateTimeImmutable('1970-01-01 00:00:00.000001');
        $timestamp = new PrecisionTimestamp($dateTime);

        $this->assertEquals('1970-01-01 00:00:00.000001', $timestamp->format());
    }

    public function testPrecisionTimestampWithMaxDate(): void
    {
        $dateTime = new DateTimeImmutable('9999-12-31 23:59:59.999999');
        $timestamp = new PrecisionTimestamp($dateTime);

        $this->assertEquals('9999-12-31 23:59:59.999999', $timestamp->format());
    }

    public function testPrecisionTimestampWithLeapYear(): void
    {
        $dateTime = new DateTimeImmutable('2024-02-29 23:59:59.999999');
        $timestamp = new PrecisionTimestamp($dateTime);

        $this->assertEquals('2024-02-29 23:59:59.999999', $timestamp->format());
    }

    public function testPrecisionTimestampWithDifferentFormats(): void
    {
        $dateTime1 = new DateTimeImmutable('2023-12-25 10:30:45');
        $dateTime2 = new DateTimeImmutable('2023-12-25T10:30:45');
        $dateTime3 = new DateTimeImmutable('2023-12-25 10:30:45+00:00');

        $timestamp1 = new PrecisionTimestamp($dateTime1);
        $timestamp2 = new PrecisionTimestamp($dateTime2);
        $timestamp3 = new PrecisionTimestamp($dateTime3);

        $this->assertEquals('2023-12-25 10:30:45.000000', $timestamp1->format());
        $this->assertEquals('2023-12-25 10:30:45.000000', $timestamp2->format());
        $this->assertEquals('2023-12-25 10:30:45.000000', $timestamp3->format());
    }
}
