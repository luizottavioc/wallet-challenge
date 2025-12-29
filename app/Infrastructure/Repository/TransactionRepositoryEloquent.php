<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use App\Domain\Entity\TransactionEntity;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Infrastructure\Eloquent\Model\Transaction;
use App\Infrastructure\Trait\EloquentModelToEntityTrait;
use Random\RandomException;

final class TransactionRepositoryEloquent implements TransactionRepositoryInterface
{
    use EloquentModelToEntityTrait;

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     * @throws InvalidValueObjectArgumentException
     */
    public function findByTransactionId(string $transactionId): ?TransactionEntity
    {
        /* @var Transaction $transaction */
        $transaction = Transaction::with('payer', 'payee')->find($transactionId);
        if (is_null($transaction)) {
            return null;
        }

        return new TransactionEntity(
            id: new Identifier($transaction->id),
            payer: $this->parseUserEntity($transaction->payer),
            payee: $this->parseUserEntity($transaction->payee),
            amount: new Money($transaction->amount),
            processedAt: new PrecisionTimestamp($transaction->processed_at->toDateTimeImmutable())
        );
    }

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