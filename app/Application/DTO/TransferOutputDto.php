<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\WalletEntity;

final readonly class TransferOutputDto
{
    public function __construct(
        public WalletEntity $payerWallet,
        public TransactionEntity $transaction,
    ) {}

    public function toArray(): array
    {
        return [
            'wallet' => [
                'id' => $this->payerWallet->getId(),
                'userId' => $this->payerWallet->getUser()->getId(),
                'amount' => $this->payerWallet->getAmount(),
                'processedAt' => $this->payerWallet->getProcessedAt(),
            ],
            'transaction' => [
                'id' => $this->transaction->getId(),
                'payerId' => $this->transaction->getPayer(),
                'payeeId' => $this->transaction->getPayee(),
                'amount' => $this->transaction->getAmount(),
                'processedAt' => $this->transaction->getProcessedAt(),
            ],
        ];
    }
}