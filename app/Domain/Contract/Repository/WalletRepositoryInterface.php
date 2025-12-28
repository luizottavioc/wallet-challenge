<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\Entity\WalletEntity;

interface WalletRepositoryInterface
{
    public function save(WalletEntity $walletEntity): void;

    public function findByUserIdLocking(int|string $userId): WalletEntity;
}