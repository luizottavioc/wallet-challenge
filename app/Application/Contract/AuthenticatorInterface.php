<?php

namespace App\Application\Contract;

use App\Application\DTO\AccessTokenDto;
use App\Domain\Entity\UserEntity;

interface AuthenticatorInterface
{
    public function generateToken(UserEntity $user): AccessTokenDto;

    public function tokenIsValid(string $token): bool;

    public function decodeToken(string $token): AccessTokenDto;
}