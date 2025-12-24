<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\UserTypeEnum;

final readonly class UserEntity
{
    public function __construct(
        private string|int $id,
        private string $name,
        private string $email,
        private string|null $cpf,
        private string|null $cnpj,
        private UserTypeEnum $type
    ) {}

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function getType(): UserTypeEnum
    {
        return $this->type;
    }
}