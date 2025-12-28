<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Entity\UserEntity;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\ValueObject\Identifier;
use App\Infrastructure\Eloquent\Model\User;
use Random\RandomException;

final class UserRepositoryEloquent implements UserRepositoryInterface
{
    /**
     * @throws RandomException
     */
    public function findUserByEmail(string $email): ?UserEntity
    {
        $user = User::query()->where('email', $email)->first();
        if (is_null($user)) {
            return null;
        }

        return new UserEntity(
            id: new Identifier($user->id),
            name: $user->name,
            email: $user->email,
            cpf: $user->cpf,
            cnpj: $user->cnpj,
            password: $user->password,
            type: UserTypeEnum::from($user->type),
        );
    }
}