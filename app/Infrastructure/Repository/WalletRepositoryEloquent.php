<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\WalletRepositoryInterface;
use App\Domain\Entity\UserEntity;
use App\Domain\Entity\WalletEntity;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Infrastructure\Eloquent\Model\Wallet;

final class WalletRepositoryEloquent implements WalletRepositoryInterface
{
    public function save(WalletEntity $walletEntity): void
    {
        Wallet::create([
            'id' => $walletEntity->getId()->getValue(),
            'user_id' => $walletEntity->getUser()->getId()->getValue(),
            'amount' => $walletEntity->getAmount()->getValue(),
            'processed_at' => $walletEntity->getProcessedAt()->format()
        ]);
    }

    public function findByUserIdLocking(int|string $userId): WalletEntity
    {
        $wallet = Wallet::query()
            ->where('user_id', $userId)
            ->orderBy('processed_at', 'desc')
            ->with('user')
            ->lockForUpdate()
            ->firstOrFail();

        return new WalletEntity(
            id: new Identifier($wallet->id),
            user: new UserEntity(
                id: new Identifier($wallet->user_id),
                name: $wallet->user->name,
                email: $wallet->user->email,
                cpf: $wallet->user->cpf,
                cnpj: $wallet->user->cnpj,
                password: $wallet->user->password,
                type: UserTypeEnum::from($wallet->user->type),
            ),
            amount: new Money($wallet->amount),
            processedAt: new PrecisionTimestamp($wallet->processed_at->toDateTimeImmutable())
        );
    }

}