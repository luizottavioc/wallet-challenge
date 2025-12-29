<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\DTO;

use App\Application\DTO\TransferOutputDto;
use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\UserEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Domain\Enum\UserTypeEnum;
use PHPUnit\Framework\TestCase;

final class TransferOutputDtoTest extends TestCase
{
    public function testTransferOutputDtoCreation(): void
    {
        $walletId = 'wallet-uuid';
        $userId = 'user-uuid';
        $transactionId = 'transaction-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Test User',
            email: 'test@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier($walletId),
            user: $user,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $payee = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: $user,
            payee: $payee,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $transferOutputDto = new TransferOutputDto($wallet, $transaction);

        $this->assertSame($wallet, $transferOutputDto->payerWallet);
        $this->assertSame($transaction, $transferOutputDto->transaction);
    }

    public function testTransferOutputDtoToArray(): void
    {
        $walletId = 'wallet-uuid';
        $userId = 'user-uuid';
        $transactionId = 'transaction-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Test User',
            email: 'test@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier($walletId),
            user: $user,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $payee = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: $user,
            payee: $payee,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $transferOutputDto = new TransferOutputDto($wallet, $transaction);
        $result = $transferOutputDto->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('wallet', $result);
        $this->assertArrayHasKey('transaction', $result);

        $this->assertArrayHasKey('id', $result['wallet']);
        $this->assertArrayHasKey('userId', $result['wallet']);
        $this->assertArrayHasKey('amount', $result['wallet']);
        $this->assertArrayHasKey('processedAt', $result['wallet']);

        $this->assertArrayHasKey('id', $result['transaction']);
        $this->assertArrayHasKey('payerId', $result['transaction']);
        $this->assertArrayHasKey('payeeId', $result['transaction']);
        $this->assertArrayHasKey('amount', $result['transaction']);
        $this->assertArrayHasKey('processedAt', $result['transaction']);

        $this->assertEquals($walletId, $result['wallet']['id']);
        $this->assertEquals($userId, $result['wallet']['userId']);
        $this->assertEquals($amount, $result['wallet']['amount']);

        $this->assertEquals($transactionId, $result['transaction']['id']);
        $this->assertEquals($userId, $result['transaction']['payerId']);
        $this->assertEquals($payeeId, $result['transaction']['payeeId']);
        $this->assertEquals($amount, $result['transaction']['amount']);
    }

    public function testTransferOutputDtoToArrayWithZeroAmount(): void
    {
        $walletId = 'wallet-uuid';
        $userId = 'user-uuid';
        $transactionId = 'transaction-uuid';
        $payeeId = 'payee-uuid';
        $amount = 0;

        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Test User',
            email: 'test@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier($walletId),
            user: $user,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $payee = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: $user,
            payee: $payee,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $transferOutputDto = new TransferOutputDto($wallet, $transaction);
        $result = $transferOutputDto->toArray();

        $this->assertEquals(0, $result['wallet']['amount']);
        $this->assertEquals(0, $result['transaction']['amount']);
    }
}
