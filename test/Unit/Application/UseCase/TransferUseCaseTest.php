<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\UseCase;

use App\Application\Contract\EventDispatcherInterface;
use App\Application\Contract\TransactionManagerInterface;
use App\Application\Contract\TransferAuthorizerInterface;
use App\Application\DTO\TransferInputDto;
use App\Application\DTO\TransferOutputDto;
use App\Application\Exception\UnauthorizedTransferException;
use App\Application\UseCase\TransferUseCase;
use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use App\Domain\Contract\Repository\WalletRepositoryInterface;
use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\UserEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Event\TransferCompletedEvent;
use App\Domain\Exception\CannotPerformTransferByInsufficientFundsException;
use App\Domain\Exception\CannotPerformTransferByUserTypeException;
use App\Domain\Exception\CannotPerformTransferForItselfException;
use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\Service\PerformTransactionDomainService;
use App\Domain\Service\UpdateWalletByTransactionDomainService;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Domain\Enum\UserTypeEnum;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TransferUseCaseTest extends TestCase
{
    private TransactionManagerInterface|MockObject $transactionManager;
    private TransferAuthorizerInterface|MockObject $transferAuthorizer;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private WalletRepositoryInterface|MockObject $walletRepository;
    private TransactionRepositoryInterface|MockObject $transactionRepository;
    private PerformTransactionDomainService|MockObject $performTransactionDomainService;
    private UpdateWalletByTransactionDomainService|MockObject $updateWalletByTransactionDomainService;
    private TransferUseCase $transferUseCase;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->transactionManager = $this->createMock(TransactionManagerInterface::class);
        $this->transferAuthorizer = $this->createMock(TransferAuthorizerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->walletRepository = $this->createMock(WalletRepositoryInterface::class);
        $this->transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $this->performTransactionDomainService = $this->createMock(PerformTransactionDomainService::class);
        $this->updateWalletByTransactionDomainService = $this->createMock(UpdateWalletByTransactionDomainService::class);

        $this->transferUseCase = new TransferUseCase(
            $this->transactionManager,
            $this->transferAuthorizer,
            $this->eventDispatcher,
            $this->walletRepository,
            $this->transactionRepository,
            $this->performTransactionDomainService,
            $this->updateWalletByTransactionDomainService
        );
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws InvalidValueObjectArgumentException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     * @throws CannotPerformTransferForItselfException
     */
    public function testTransferSuccessfully(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;
        $transactionId = 'transaction-uuid';

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

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

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: $payerUser,
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $updatedWalletPayer = new WalletEntity(
            id: $walletPayer->getId(),
            user: $walletPayer->getUser(),
            amount: new Money(10000),
            processedAt: new PrecisionTimestamp()
        );

        $transferOutputDto = new TransferOutputDto($updatedWalletPayer, $transaction);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willReturn($transferOutputDto);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($transactionId) {
                return $event instanceof TransferCompletedEvent && $event->transactionId === $transactionId;
            }));

        $result = $this->transferUseCase->transfer($transferInputDto);

        $this->assertSame($updatedWalletPayer, $result->payerWallet);
        $this->assertSame($transaction, $result->transaction);
    }

    public function testTransferWithUnauthorizedTransferException(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(false);

        $this->transactionManager
            ->expects($this->never())
            ->method('run');

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(UnauthorizedTransferException::class);

        $this->transferUseCase->transfer($transferInputDto);
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws InvalidValueObjectArgumentException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     * @throws CannotPerformTransferForItselfException
     */
    public function testTransferWithShopkeeperUserThrowsException(): void
    {
        $payerId = 'shopkeeper-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new CannotPerformTransferByUserTypeException());

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(CannotPerformTransferByUserTypeException::class);

        $this->transferUseCase->transfer($transferInputDto);
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws InvalidValueObjectArgumentException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     * @throws CannotPerformTransferForItselfException
     */
    public function testTransferWithInsufficientFundsThrowsException(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new CannotPerformTransferByInsufficientFundsException());

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(CannotPerformTransferByInsufficientFundsException::class);

        $this->transferUseCase->transfer($transferInputDto);
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws InvalidValueObjectArgumentException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     * @throws CannotPerformTransferForItselfException
     */
    public function testTransferForItselfThrowsException(): void
    {
        $payerId = 'user-uuid';
        $payeeId = 'user-uuid';
        $amount = 10000;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new CannotPerformTransferForItselfException());

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(CannotPerformTransferForItselfException::class);

        $this->transferUseCase->transfer($transferInputDto);
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws InvalidValueObjectArgumentException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     * @throws CannotPerformTransferForItselfException
     */
    public function testTransferWithInvalidAmountThrowsException(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = -100;

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new InvalidValueObjectArgumentException());

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatch');

        $this->expectException(InvalidValueObjectArgumentException::class);

        $this->transferUseCase->transfer($transferInputDto);
    }

    /**
     * @throws UnauthorizedTransferException
     * @throws InvalidValueObjectArgumentException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     * @throws CannotPerformTransferForItselfException
     */
    public function testTransferWithZeroAmountWorksSuccessfully(): void
    {
        $payerId = 'payer-uuid';
        $payeeId = 'payee-uuid';
        $amount = 0;
        $transactionId = 'transaction-uuid';

        $transferInputDto = new TransferInputDto($payerId, $payeeId, $amount);

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

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: $payerUser,
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $transferOutputDto = new TransferOutputDto($walletPayer, $transaction);

        $this->transferAuthorizer
            ->expects($this->once())
            ->method('authorize')
            ->willReturn(true);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willReturn($transferOutputDto);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($transactionId) {
                return $event instanceof TransferCompletedEvent && $event->transactionId === $transactionId;
            }));

        $result = $this->transferUseCase->transfer($transferInputDto);

        $this->assertSame($walletPayer, $result->payerWallet);
        $this->assertSame($transaction, $result->transaction);
    }
}
