<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Entity\UserEntity;
use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Eloquent\Model\User;

final class UserRepositoryEloquent implements UserRepositoryInterface
{
    public function findUserByEmail(string $email): ?UserEntity
    {
        $user = User::query()->where('email', $email)->first();
        if (is_null($user)) {
            return null;
        }

        return new UserEntity(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            cpf: $user->cpf,
            cnpj: $user->cnpj,
            password: $user->password,
            type: UserTypeEnum::from($user->type),
        );
    }
}