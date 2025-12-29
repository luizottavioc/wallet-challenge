<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\Entity\TransactionEntity;

interface TransactionRepositoryInterface
{
    public function findByTransactionId(string $transactionId): ?TransactionEntity;
    public function save(TransactionEntity $transactionEntity): void;
}