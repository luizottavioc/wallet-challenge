<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Domain\Service;

use App\Domain\Entity\UserEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Exception\CannotPerformTransferByInsufficientFundsException;
use App\Domain\Exception\CannotPerformTransferByUserTypeException;
use App\Domain\Exception\CannotPerformTransferForItselfException;
use App\Domain\Service\PerformTransactionDomainService;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Domain\Enum\UserTypeEnum;
use PHPUnit\Framework\TestCase;

final class PerformTransactionDomainServiceTest extends TestCase
{
    private PerformTransactionDomainService $service;

    protected function setUp(): void
    {
        $this->service = new PerformTransactionDomainService();
    }

    public function testExecuteSuccessfully(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';

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

        $walletPayer = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $walletPayee = new WalletEntity(
            id: new Identifier(),
            user: $payeeUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $amount = new Money(10000);

        $transaction = $this->service->execute($walletPayer, $walletPayee, $amount);

        $this->assertSame($payerUser, $transaction->getPayer());
        $this->assertSame($payeeUser, $transaction->getPayee());
        $this->assertSame($amount, $transaction->getAmount());
    }

    public function testExecuteWithShopkeeperUserThrowsException(): void
    {
        $payerId = 'shopkeeper-uuid';
        $payeeId = 'payee-uuid';

        $payerUser = new UserEntity(
            id: new Identifier($payerId),
            name: 'Shopkeeper User',
            email: 'shopkeeper@example.com',
            cpf: null,
            cnpj: '12345678901234',
            password: 'hashed_password',
            type: UserTypeEnum::SHOPKEEPER
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

        $walletPayer = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $walletPayee = new WalletEntity(
            id: new Identifier(),
            user: $payeeUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $amount = new Money(10000);

        $this->expectException(CannotPerformTransferByUserTypeException::class);

        $this->service->execute($walletPayer, $walletPayee, $amount);
    }

    public function testExecuteForItselfThrowsException(): void
    {
        $userId = 'user-uuid';

        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'User',
            email: 'user@example.com',
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $walletPayer = new WalletEntity(
            id: new Identifier(),
            user: $user,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $walletPayee = new WalletEntity(
            id: new Identifier(),
            user: $user,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $amount = new Money(10000);

        $this->expectException(CannotPerformTransferForItselfException::class);

        $this->service->execute($walletPayer, $walletPayee, $amount);
    }

    public function testExecuteWithInsufficientFundsThrowsException(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';

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

        $walletPayer = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $walletPayee = new WalletEntity(
            id: new Identifier(),
            user: $payeeUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $amount = new Money(10000);

        $this->expectException(CannotPerformTransferByInsufficientFundsException::class);

        $this->service->execute($walletPayer, $walletPayee, $amount);
    }

    public function testExecuteWithExactFunds(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';

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

        $walletPayer = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(10000),
            processedAt: new PrecisionTimestamp()
        );

        $walletPayee = new WalletEntity(
            id: new Identifier(),
            user: $payeeUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $amount = new Money(10000);

        $transaction = $this->service->execute($walletPayer, $walletPayee, $amount);

        $this->assertSame($payerUser, $transaction->getPayer());
        $this->assertSame($payeeUser, $transaction->getPayee());
        $this->assertSame($amount, $transaction->getAmount());
    }

    public function testExecuteWithZeroAmount(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';

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

        $walletPayer = new WalletEntity(
            id: new Identifier(),
            user: $payerUser,
            amount: new Money(20000),
            processedAt: new PrecisionTimestamp()
        );

        $walletPayee = new WalletEntity(
            id: new Identifier(),
            user: $payeeUser,
            amount: new Money(5000),
            processedAt: new PrecisionTimestamp()
        );

        $amount = new Money(0);

        $transaction = $this->service->execute($walletPayer, $walletPayee, $amount);

        $this->assertSame($payerUser, $transaction->getPayer());
        $this->assertSame($payeeUser, $transaction->getPayee());
        $this->assertSame($amount, $transaction->getAmount());
    }
}
