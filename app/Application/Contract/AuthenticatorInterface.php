<?php

namespace App\Application\Contract;

use App\Domain\Entity\AuthEntity;

interface AuthenticatorInterface
{
    public function tokenIsValid(string $token): bool;
    public function decodeTokenToAuthEntity(string $token): AuthEntity;
}