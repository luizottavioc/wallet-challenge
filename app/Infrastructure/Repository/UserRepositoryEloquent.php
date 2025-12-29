<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Entity\UserEntity;
use App\Infrastructure\Eloquent\Model\User;
use App\Infrastructure\Trait\EloquentModelToEntityTrait;
use Random\RandomException;

final class UserRepositoryEloquent implements UserRepositoryInterface
{
    use EloquentModelToEntityTrait;

    /**
     * @throws RandomException
     */
    public function findUserByEmail(string $email): ?UserEntity
    {
        $user = User::query()->where('email', $email)->first();
        if (is_null($user)) {
            return null;
        }

        return $this->parseUserEntity($user);
    }
}