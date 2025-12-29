<?php

declare(strict_types=1);

namespace App\Infrastructure\Trait;

use App\Domain\Entity\UserEntity;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\ValueObject\Identifier;
use App\Infrastructure\Eloquent\Model\User;
use Random\RandomException;

trait EloquentModelToEntityTrait
{
    /**
     * @throws RandomException
     */
    public function parseUserEntity(User $user): UserEntity
    {
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