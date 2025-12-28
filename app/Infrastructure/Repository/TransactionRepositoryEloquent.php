<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use App\Domain\Entity\TransactionEntity;
use App\Infrastructure\Eloquent\Model\Transaction;

final class TransactionRepositoryEloquent implements TransactionRepositoryInterface
{
    public function save(TransactionEntity $transactionEntity): void
    {
        Transaction::create([
            'id' => $transactionEntity->getId()->getValue(),
            'payer_id' => $transactionEntity->getPayer()->getId()->getValue(),
            'payee_id' => $transactionEntity->getPayee()->getId()->getValue(),
            'amount' => $transactionEntity->getAmount()->getValue(),
            'processed_at' => $transactionEntity->getProcessedAt()->format()
        ]);
    }

}