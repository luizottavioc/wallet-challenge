<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\UserTypeEnum;

final class UserEntity
{
    public function __construct(
        private readonly string|int $id,
        private readonly string $name,
        private readonly string $email,
        private readonly string|null $cpf,
        private readonly string|null $cnpj,
        private readonly UserTypeEnum $type
    ) {}
}