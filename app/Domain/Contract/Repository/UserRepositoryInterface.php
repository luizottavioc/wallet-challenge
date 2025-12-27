<?php

declare(strict_types=1);

namespace App\Domain\Contract\Repository;

use App\Domain\Entity\UserEntity;

interface UserRepositoryInterface
{
    public function findUserByEmail(string $email): ?UserEntity;
}