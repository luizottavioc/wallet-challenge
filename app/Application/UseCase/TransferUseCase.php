<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Contract\EventDispatcherInterface;
use App\Application\Contract\TransactionManagerInterface;
use App\Application\Contract\TransferAuthorizerInterface;
use App\Application\DTO\TransferInputDto;
use App\Application\DTO\TransferOutputDto;
use App\Application\Exception\UnauthorizedTransferException;
use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use App\Domain\Contract\Repository\WalletRepositoryInterface;
use App\Domain\Event\TransferCompletedEvent;
use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\Exception\CannotPerformTransferByInsufficientFundsException;
use App\Domain\Exception\CannotPerformTransferByUserTypeException;
use App\Domain\Service\PerformTransactionDomainService;
use App\Domain\Service\UpdateWalletByTransactionDomainService;
use App\Domain\ValueObject\Money;

final readonly class TransferUseCase
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private TransferAuthorizerInterface $transferAuthorizer,
        private EventDispatcherInterface $eventDispatcher,
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private PerformTransactionDomainService $performTransactionDomainService,
        private UpdateWalletByTransactionDomainService $updateWalletByTransactionDomainService
    ) {}

    /**
     * @throws InvalidValueObjectArgumentException
     * @throws UnauthorizedTransferException
     * @throws CannotSubtractAmountException
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     */
    public function transfer(TransferInputDto $transferInputDto): TransferOutputDto
    {
        if (!$this->transferAuthorizer->authorize()) {
            throw new UnauthorizedTransferException();
        }

        /* @var TransferOutputDto $transferOutputDto */
        $transferOutputDto = $this->transactionManager->run(function () use ($transferInputDto): TransferOutputDto {
            $walletPayer = $this->walletRepository->findByUserIdLocking($transferInputDto->payerId);
            $walletPayee = $this->walletRepository->findByUserIdLocking($transferInputDto->payeeId);
            $amount = new Money($transferInputDto->amount);

            $transaction = $this->performTransactionDomainService->execute($walletPayer, $walletPayee, $amount);
            $this->transactionRepository->save($transaction);

            $newWalletPayer = $this->updateWalletByTransactionDomainService->execute($walletPayer, $transaction);
            $this->walletRepository->save($newWalletPayer);

            $newWalletPayee = $this->updateWalletByTransactionDomainService->execute($walletPayee, $transaction);
            $this->walletRepository->save($newWalletPayee);

            return new TransferOutputDto(
                payerWallet: $newWalletPayer,
                transaction: $transaction
            );
        });

        $this->eventDispatcher->dispatch(
            new TransferCompletedEvent($transferOutputDto->transaction->getId()->getValue())
        );

        return $transferOutputDto;
    }
}