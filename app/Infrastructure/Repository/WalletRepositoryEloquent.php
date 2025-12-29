<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\WalletRepositoryInterface;
use App\Domain\Entity\WalletEntity;
use App\Domain\Exception\InvalidValueObjectArgumentException;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Infrastructure\Eloquent\Model\Wallet;
use App\Infrastructure\Trait\EloquentModelToEntityTrait;
use Random\RandomException;

final class WalletRepositoryEloquent implements WalletRepositoryInterface
{
    use EloquentModelToEntityTrait;

    public function save(WalletEntity $walletEntity): void
    {
        Wallet::create([
            'id' => $walletEntity->getId()->getValue(),
            'user_id' => $walletEntity->getUser()->getId()->getValue(),
            'amount' => $walletEntity->getAmount()->getValue(),
            'processed_at' => $walletEntity->getProcessedAt()->format()
        ]);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     * @throws InvalidValueObjectArgumentException
     */
    public function findByUserIdLocking(int|string $userId): WalletEntity
    {
        /* @var Wallet $wallet */
        $wallet = Wallet::query()
            ->where('user_id', $userId)
            ->orderBy('processed_at', 'desc')
            ->with('user')
            ->lockForUpdate()
            ->firstOrFail();

        return new WalletEntity(
            id: new Identifier($wallet->id),
            user: $this->parseUserEntity($wallet->user),
            amount: new Money($wallet->amount),
            processedAt: new PrecisionTimestamp($wallet->processed_at->toDateTimeImmutable())
        );
    }

}