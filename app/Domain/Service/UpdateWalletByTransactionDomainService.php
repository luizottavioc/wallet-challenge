<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Exception\CannotSubtractAmountException;
use App\Domain\Exception\InvalidValueObjectArgumentException;

class UpdateWalletByTransactionDomainService
{
    /**
     * @throws CannotSubtractAmountException
     * @throws InvalidValueObjectArgumentException
     */
    public function execute(WalletEntity $wallet, TransactionEntity $transactionEntity): WalletEntity
    {
        $amount = $transactionEntity->getAmount();
        $userIsPayer = $wallet->getUser()->isEqualTo($transactionEntity->getPayer());

        if ($userIsPayer) {
            return $wallet->debit($amount);
        }

        return $wallet->credit($amount);
    }
}