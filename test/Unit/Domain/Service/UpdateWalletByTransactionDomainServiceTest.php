<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Domain\Service;

use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\UserEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Service\UpdateWalletByTransactionDomainService;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Domain\Enum\UserTypeEnum;
use PHPUnit\Framework\TestCase;

final class UpdateWalletByTransactionDomainServiceTest extends TestCase
{
    private UpdateWalletByTransactionDomainService $service;

    protected function setUp(): void
    {
        $this->service = new UpdateWalletByTransactionDomainService();
    }

    public function testExecuteWithPayerWallet(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $payerUser = new UserEntity(
            id: new Identifier($payerId),
            name: 'Payer User',
            email: 'payer@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $payeeUser = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $transaction = new TransactionEntity(
            id: new Identifier(),
            payer: $payerUser,
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $updatedWallet = $this->service->execute($wallet, $transaction);

        $this->assertEquals(10000, $updatedWallet->getAmount()->getValue());
        $this->assertSame($wallet->getUser(), $updatedWallet->getUser());
        $this->assertNotEquals($wallet->getId(), $updatedWallet->getId());
    }

    public function testExecuteWithPayeeWallet(): void
    {
        $userId = 'user-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $payerUser = new UserEntity(
            id: new Identifier($userId),
            name: 'Payer User',
            email: 'payer@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $payeeUser = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier(),
            user: $payeeUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $transaction = new TransactionEntity(
            id: new Identifier(),
            payer: $payerUser,
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $updatedWallet = $this->service->execute($wallet, $transaction);

        $this->assertEquals(15000, $updatedWallet->getAmount()->getValue());
        $this->assertSame($wallet->getUser(), $updatedWallet->getUser());
        $this->assertNotEquals($wallet->getId(), $updatedWallet->getId());
    }

    public function testExecuteWithZeroAmount(): void
    {
        $userId = 'user-uuid';
        $payeeId = 'payee-uuid';
        $amount = 0;

        $payerUser = new UserEntity(
            id: new Identifier($userId),
            name: 'Payer User',
            email: 'payer@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $payeeUser = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $transaction = new TransactionEntity(
            id: new Identifier(),
            payer: $payerUser,
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $updatedWallet = $this->service->execute($wallet, $transaction);

        $this->assertEquals(20000, $updatedWallet->getAmount()->getValue());
    }

    public function testExecuteWithExactAmountDebit(): void
    {
        $userId = 'user-uuid';
        $payeeId = 'payee-uuid';
        $amount = 20000;

        $payerUser = new UserEntity(
            id: new Identifier($userId),
            name: 'Payer User',
            email: 'payer@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $payeeUser = new UserEntity(
            id: new Identifier($payeeId),
            name: 'Payee User',
            email: 'payee@example.com',
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $wallet = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $transaction = new TransactionEntity(
            id: new Identifier(),
            payer: $payerUser,
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $updatedWallet = $this->service->execute($wallet, $transaction);

        $this->assertEquals(0, $updatedWallet->getAmount()->getValue());
    }
}
