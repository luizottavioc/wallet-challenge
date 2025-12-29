<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\DTO;

use App\Application\DTO\TransferInputDto;
use PHPUnit\Framework\TestCase;

final class TransferInputDtoTest extends TestCase
{
    public function testTransferInputDtoCreation(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->assertEquals($payerId, $transferInputDto->payerId);
        $this->assertEquals($payeeId, $transferInputDto->payeeId);
        $this->assertEquals($amount, $transferInputDto->amount);
    }

    public function testTransferInputDtoWithZeroAmount(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 0;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->assertEquals($payerId, $transferInputDto->payerId);
        $this->assertEquals($payeeId, $transferInputDto->payeeId);
        $this->assertEquals($amount, $transferInputDto->amount);
    }

    public function testTransferInputDtoWithLargeAmount(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 999999999;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->assertEquals($payerId, $transferInputDto->payerId);
        $this->assertEquals($payeeId, $transferInputDto->payeeId);
        $this->assertEquals($amount, $transferInputDto->amount);
    }

    public function testTransferInputDtoWithSamePayerAndPayee(): void
    {
        $userId = 'user-uuid';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($userId, $userId, $amount);

        $this->assertEquals($userId, $transferInputDto->payerId);
        $this->assertEquals($userId, $transferInputDto->payeeId);
        $this->assertEquals($amount, $transferInputDto->amount);
    }

    public function testTransferInputDtoWithNegativeAmount(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = -1000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->assertEquals($payerId, $transferInputDto->payerId);
        $this->assertEquals($payeeId, $transferInputDto->payeeId);
        $this->assertEquals($amount, $transferInputDto->amount);
    }

    public function testTransferInputDtoWithEmptyIds(): void
    {
        $payerId = '';
        $payeeId = '';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->assertEquals($payerId, $transferInputDto->payerId);
        $this->assertEquals($payeeId, $transferInputDto->payeeId);
        $this->assertEquals($amount, $transferInputDto->amount);
    }
}
