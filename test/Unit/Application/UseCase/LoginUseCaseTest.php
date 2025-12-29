<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\UseCase;

use App\Application\Contract\AuthenticatorInterface;
use App\Application\DTO\AccessTokenDto;
use App\Application\DTO\LoginInputDto;
use App\Application\Exception\GenericLoginException;
use App\Application\UseCase\LoginUseCase;
use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Contract\Service\PasswordHasherInterface;
use App\Domain\Entity\UserEntity;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\InvalidPasswordException;
use App\Domain\ValueObject\Identifier;
use Hyperf\Stringable\Str;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

final class LoginUseCaseTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private AuthenticatorInterface|MockObject $authenticator;
    private PasswordHasherInterface|MockObject $passwordHasher;
    private LoginUseCase $loginUseCase;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->authenticator = $this->createMock(AuthenticatorInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $this->loginUseCase = new LoginUseCase(
            $this->userRepository,
            $this->authenticator,
            $this->passwordHasher
        );
    }

    /**
     * @throws GenericLoginException
     */
    public function testLoginSuccessfully(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;
        $token = 'generated.jwt.token';
        $expiresAt = time() + 3600;

        $loginInputDto = new LoginInputDto($email, $password);
        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: time(),
            expiresAt: $expiresAt
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('check')
            ->with($password, 'hashed_password')
            ->willReturn(true);

        $this->authenticator
            ->expects($this->once())
            ->method('generateToken')
            ->with($user)
            ->willReturn($accessTokenDto);

        $result = $this->loginUseCase->login($loginInputDto);

        $this->assertEquals($token, $result->token);
        $this->assertEquals($expiresAt, $result->expiresAt);
    }

    public function testLoginWithNonExistentEmailThrowsException(): void
    {
        $email = 'nonexistent@example.com';
        $password = 'password123';

        $loginInputDto = new LoginInputDto($email, $password);

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn(null);

        $this->passwordHasher
            ->expects($this->never())
            ->method('check');

        $this->authenticator
            ->expects($this->never())
            ->method('generateToken');

        $this->expectException(GenericLoginException::class);
        $this->expectExceptionMessage('authentication_failed');

        $this->loginUseCase->login($loginInputDto);
    }

    public function testLoginWithInvalidPasswordThrowsException(): void
    {
        $email = 'test@example.com';
        $password = 'wrong_password';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;

        $loginInputDto = new LoginInputDto($email, $password);
        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('check')
            ->with($password, 'hashed_password')
            ->willReturn(false);

        $this->authenticator
            ->expects($this->never())
            ->method('generateToken');

        $this->expectException(GenericLoginException::class);
        $this->expectExceptionMessage('authentication_failed');

        $this->loginUseCase->login($loginInputDto);
    }

    public function testLoginWithPasswordHasherExceptionWrapsInGenericException(): void
    {
        $email = 'test@example.com';
        $password = 'password123';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;

        $loginInputDto = new LoginInputDto($email, $password);
        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Test User',
            email: $email,
            cpf: null,
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('check')
            ->with($password, 'hashed_password')
            ->willThrowException(new InvalidPasswordException());

        $this->authenticator
            ->expects($this->never())
            ->method('generateToken');

        $this->expectException(GenericLoginException::class);
        $this->expectExceptionMessage('authentication_failed');

        $this->loginUseCase->login($loginInputDto);
    }

    /**
     * @throws GenericLoginException
     */
    public function testLoginWithShopkeeperUserSuccessfully(): void
    {
        $email = 'shopkeeper@example.com';
        $password = 'password123';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::SHOPKEEPER;
        $token = 'generated.jwt.token';
        $expiresAt = time() + 3600;

        $loginInputDto = new LoginInputDto($email, $password);
        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'Shopkeeper User',
            email: $email,
            cpf: null,
            cnpj: '12345678901234',
            password: 'hashed_password',
            type: $userType
        );

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: time(),
            expiresAt: $expiresAt
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('check')
            ->with($password, 'hashed_password')
            ->willReturn(true);

        $this->authenticator
            ->expects($this->once())
            ->method('generateToken')
            ->with($user)
            ->willReturn($accessTokenDto);

        $result = $this->loginUseCase->login($loginInputDto);

        $this->assertEquals($token, $result->token);
        $this->assertEquals($expiresAt, $result->expiresAt);
    }

    /**
     * @throws GenericLoginException
     * @throws RandomException
     */
    public function testLoginWithUserHavingCpfSuccessfully(): void
    {
        $email = 'user@example.com';
        $password = 'password123';
        $userId = 'user-uuid';
        $userType = UserTypeEnum::DEFAULT;
        $token = 'generated.jwt.token';
        $expiresAt = time() + 3600;

        $loginInputDto = new LoginInputDto($email, $password);
        $user = new UserEntity(
            id: new Identifier($userId),
            name: 'User with CPF',
            email: $email,
            cpf: '12345678901',
            cnpj: null,
            password: 'hashed_password',
            type: $userType
        );

        $accessTokenDto = new AccessTokenDto(
            token: $token,
            userId: $userId,
            userType: $userType,
            createdAt: time(),
            expiresAt: $expiresAt
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('check')
            ->with($password, 'hashed_password')
            ->willReturn(true);

        $this->authenticator
            ->expects($this->once())
            ->method('generateToken')
            ->with($user)
            ->willReturn($accessTokenDto);

        $result = $this->loginUseCase->login($loginInputDto);

        $this->assertEquals($token, $result->token);
        $this->assertEquals($expiresAt, $result->expiresAt);
    }
}
