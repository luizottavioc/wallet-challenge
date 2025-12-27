<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class LoginInputDto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}