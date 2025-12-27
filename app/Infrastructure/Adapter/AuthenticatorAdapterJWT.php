<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\AuthenticatorInterface;
use App\Application\DTO\AccessTokenDto;
use App\Domain\Entity\UserEntity;
use App\Domain\Enum\UserTypeEnum;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;
use function Hyperf\Config\config;

final class AuthenticatorAdapterJWT implements AuthenticatorInterface
{
    private const string ENCODE_ALGORITHM = 'HS256';
    private const int TOKEN_EXPIRATION_IN_SECONDS = 3600;

    private function getJwtSecretKey(): string
    {
        return config("auth.jwt_secret_key");
    }

    public function generateToken(UserEntity $user): AccessTokenDto
    {
        $now = time();
        $expirationTime = $now + self::TOKEN_EXPIRATION_IN_SECONDS;

        $tokenPayload = [
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'type' => $user->getType()->value,
            'iat' => $now,
            'exp' => $expirationTime,
        ];

        $token = JWT::encode(
            $tokenPayload,
            $this->getJwtSecretKey(),
            self::ENCODE_ALGORITHM
        );

        return new AccessTokenDto(
            token: $token,
            userId: $user->getId(),
            userType: $user->getType(),
            createdAt: $now,
            expiresAt: $expirationTime,
        );
    }

    public function tokenIsValid(string $token): bool
    {
        try {
            $decodedToken = $this->decodeToken($token);
            return $decodedToken->getExpiresAt() > time();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function decodeToken(string $token): AccessTokenDto
    {
        $decodedToken = JWT::decode(
            $token,
            new Key($this->getJwtSecretKey(), self::ENCODE_ALGORITHM)
        );

        return new AccessTokenDto(
            token: $token,
            userId: $decodedToken->sub,
            userType: UserTypeEnum::from($decodedToken->type),
            createdAt: $decodedToken->iat,
            expiresAt: $decodedToken->exp,
        );
    }
}