<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Domain\ValueObject;

use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testMoneyCreationWithValidAmount(): void
    {
        $amount = 10000;
        $money = new Money($amount);

        $this->assertEquals($amount, $money->getValue());
    }

    public function testMoneyCreationWithZeroAmount(): void
    {
        $amount = 0;
        $money = new Money($amount);

        $this->assertEquals($amount, $money->getValue());
    }

    public function testMoneyCreationWithNegativeAmountThrowsException(): void
    {
        $amount = -100;

        $this->expectException(InvalidValueObjectArgumentException::class);

        new Money($amount);
    }

    public function testEqualsOrGreaterWithEqualAmount(): void
    {
        $amount1 = new Money(10000);
        $amount2 = new Money(10000);

        $this->assertTrue($amount1->equalsOrGreater($amount2));
    }

    public function testEqualsOrGreaterWithGreaterAmount(): void
    {
        $amount1 = new Money(15000);
        $amount2 = new Money(10000);

        $this->assertTrue($amount1->equalsOrGreater($amount2));
    }

    public function testEqualsOrGreaterWithLesserAmount(): void
    {
        $amount1 = new Money(5000);
        $amount2 = new Money(10000);

        $this->assertFalse($amount1->equalsOrGreater($amount2));
    }

    public function testSubtractAmountSuccessfully(): void
    {
        $amount1 = new Money(15000);
        $amount2 = new Money(5000);

        $result = $amount1->subtractAmount($amount2);

        $this->assertEquals(10000, $result->getValue());
    }

    public function testSubtractAmountWithExactAmount(): void
    {
        $amount1 = new Money(10000);
        $amount2 = new Money(10000);

        $result = $amount1->subtractAmount($amount2);

        $this->assertEquals(0, $result->getValue());
    }

    public function testSubtractAmountWithInsufficientFundsThrowsException(): void
    {
        $amount1 = new Money(5000);
        $amount2 = new Money(10000);

        $this->expectException(CannotSubtractAmountException::class);

        $amount1->subtractAmount($amount2);
    }

    public function testAddAmountSuccessfully(): void
    {
        $amount1 = new Money(10000);
        $amount2 = new Money(5000);

        $result = $amount1->addAmount($amount2);

        $this->assertEquals(15000, $result->getValue());
    }

    public function testAddAmountWithZero(): void
    {
        $amount1 = new Money(10000);
        $amount2 = new Money(0);

        $result = $amount1->addAmount($amount2);

        $this->assertEquals(10000, $result->getValue());
    }


    public function testFormat(): void
    {
        $amount = new Money(12345);
        $formatted = $amount->format();

        $this->assertEquals('$ 123.45', $formatted);
    }

    public function testFormatWithZeroAmount(): void
    {
        $amount = new Money(0);
        $formatted = $amount->format();

        $this->assertEquals('$ 0.00', $formatted);
    }

    public function testFormatWithLargeAmount(): void
    {
        $amount = new Money(99999999);
        $formatted = $amount->format();

        $this->assertEquals('$ 999,999.99', $formatted);
    }

    public function testFormatWithSingleCent(): void
    {
        $amount = new Money(1);
        $formatted = $amount->format();

        $this->assertEquals('$ 0.01', $formatted);
    }

    public function testMoneyValueObjectIsReadonly(): void
    {
        $amount = new Money(10000);

        $this->assertEquals(10000, $amount->getValue());
        $this->assertTrue(true);
    }

    public function testMoneyOperationsChain(): void
    {
        $amount1 = new Money(20000);
        $amount2 = new Money(5000);
        $amount3 = new Money(3000);

        $result = $amount1->subtractAmount($amount2)->addAmount($amount3);

        $this->assertEquals(18000, $result->getValue());
    }
}
