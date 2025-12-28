<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Exception\CannotPerformTransferByInsufficientFundsException;
use App\Domain\Exception\CannotPerformTransferByUserTypeException;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;

class PerformTransactionDomainService
{
    /**
     * @throws CannotPerformTransferByInsufficientFundsException
     * @throws CannotPerformTransferByUserTypeException
     */
    public function execute(WalletEntity $walletPayer, WalletEntity $walletPayee, Money $amount): TransactionEntity
    {
        if (!$walletPayer->getUser()->canPerformTransfer()) {
            throw new CannotPerformTransferByUserTypeException();
        }

        if ($walletPayer->getUser()->isEqualTo($walletPayee->getUser())) {
            throw new CannotPerformTransferByUserTypeException();
        }

        if (!$walletPayer->hasFunds($amount)) {
            throw new CannotPerformTransferByInsufficientFundsException();
        }

        return new TransactionEntity(
            id: new Identifier(),
            payer: $walletPayer->getUser(),
            payee: $walletPayee->getUser(),
            amount: $amount,
            processedAt: new PrecisionTimestamp()
        );
    }
}