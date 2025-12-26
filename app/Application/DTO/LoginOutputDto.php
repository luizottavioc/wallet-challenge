<?php

declare(strict_types=1);

namespace App\Application\DTO;

class LoginOutputDto
{
    public function __construct(
        public string $token,
        public int $expiresAt,
    ) {}

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'expiresAt' => $this->expiresAt,
        ];
    }
}