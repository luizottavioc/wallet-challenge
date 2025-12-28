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
                'id' => $this->payerWallet->getId()->getValue(),
                'userId' => $this->payerWallet->getUser()->getId()->getValue(),
                'amount' => $this->payerWallet->getAmount()->getValue(),
                'processedAt' => $this->payerWallet->getProcessedAt()->format(),
            ],
            'transaction' => [
                'id' => $this->transaction->getId()->getValue(),
                'payerId' => $this->transaction->getPayer()->getId()->getValue(),
                'payeeId' => $this->transaction->getPayee()->getId()->getValue(),
                'amount' => $this->transaction->getAmount()->getValue(),
                'processedAt' => $this->transaction->getProcessedAt()->format(),
            ],
        ];
    }
}