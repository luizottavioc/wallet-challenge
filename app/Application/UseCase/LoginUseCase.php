<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Contract\AuthenticatorInterface;
use App\Application\DTO\LoginInputDto;
use App\Application\DTO\LoginOutputDto;
use App\Application\Exception\GenericLoginException;
use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Contract\Service\PasswordHasherInterface;
use App\Domain\Exception\InvalidPasswordException;

class LoginUseCase
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected AuthenticatorInterface $authenticator,
        protected PasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @throws GenericLoginException
     */
    public function login(LoginInputDto $loginInputDto): LoginOutputDto
    {
        try {
            $user = $this->userRepository->findUserByEmail($loginInputDto->email);
            if (is_null($user)) {
                throw new GenericLoginException();
            }

            $user->verifyPassword($loginInputDto->password, $this->passwordHasher);
            $accessToken = $this->authenticator->generateToken($user);

            return new LoginOutputDto(
                token: $accessToken->getToken(),
                expiresAt: $accessToken->getExpiresAt(),
            );
        } catch (InvalidPasswordException $throwable) {
            throw new GenericLoginException($throwable);
        }
    }
}