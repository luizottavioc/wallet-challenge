<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Exception\UserHasNoFundsToPerformTransferException;
use App\Domain\Exception\UserTypeCannotPerformTransferException;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;

class PerformTransactionDomainService
{
    /**
     * @throws UserHasNoFundsToPerformTransferException
     * @throws UserTypeCannotPerformTransferException
     */
    public function execute(WalletEntity $walletPayer, WalletEntity $walletPayee, Money $amount): TransactionEntity
    {
        if (!$walletPayer->getUser()->canPerformTransfer()) {
            throw new UserTypeCannotPerformTransferException();
        }

        if (!$walletPayer->hasFunds($amount)) {
            throw new UserHasNoFundsToPerformTransferException();
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