<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Contract\Service\PasswordHasherInterface;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidPasswordException;
use App\Domain\ValueObject\Identifier;

final readonly class UserEntity
{
    public function __construct(
        private Identifier $id,
        private string $name,
        private string $email,
        private string|null $cpf,
        private string|null $cnpj,
        private string $password,
        private UserTypeEnum $type
    ) {}

    public function getId(): Identifier
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): UserTypeEnum
    {
        return $this->type;
    }

    /**
     * @throws InvalidPasswordException
     */
    public function verifyPassword(string $plainPassword, PasswordHasherInterface $hasher): void
    {
        if (!$hasher->check($plainPassword, $this->password)) {
            throw new InvalidPasswordException();
        }
    }

    public function canPerformTransfer(): bool
    {
        return $this->type == UserTypeEnum::DEFAULT;
    }

    public function isEqualTo(UserEntity $user): bool
    {
        return $this->id->getValue() === $user->getId()->getValue();
    }
}