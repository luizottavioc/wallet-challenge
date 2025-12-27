<?php

namespace App\Domain\Contract\Service;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;

    public function check(string $plainPassword, string $hashedPassword): bool;
}