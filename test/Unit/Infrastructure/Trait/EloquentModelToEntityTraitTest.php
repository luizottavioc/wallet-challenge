<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Trait;

use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Eloquent\Model\User;
use App\Infrastructure\Trait\EloquentModelToEntityTrait;
use PHPUnit\Framework\TestCase;

class TestClassWithTrait
{
    use EloquentModelToEntityTrait;
}

final class EloquentModelToEntityTraitTest extends TestCase
{
    private TestClassWithTrait $traitInstance;

    protected function setUp(): void
    {
        $this->traitInstance = new TestClassWithTrait();
    }

    public function testParseUserEntityWithDefaultUser(): void
    {
        $userModel = new User();
        $userModel->id = 'user-uuid';
        $userModel->name = 'Test User';
        $userModel->email = 'test@example.com';
        $userModel->cpf = '12345678901';
        $userModel->cnpj = null;
        $userModel->password = 'hashed_password';
        $userModel->type = UserTypeEnum::DEFAULT->value;

        $userEntity = $this->traitInstance->parseUserEntity($userModel);

        $this->assertEquals('user-uuid', $userEntity->getId()->getValue());
        $this->assertEquals('Test User', $userEntity->getName());
        $this->assertEquals('test@example.com', $userEntity->getEmail());
        $this->assertEquals('12345678901', $userEntity->getCpf());
        $this->assertNull($userEntity->getCnpj());
        $this->assertEquals(UserTypeEnum::DEFAULT, $userEntity->getType());
    }

    public function testParseUserEntityWithShopkeeperUser(): void
    {
        $userModel = new User();
        $userModel->id = 'shopkeeper-uuid';
        $userModel->name = 'Shopkeeper User';
        $userModel->email = 'shopkeeper@example.com';
        $userModel->cpf = null;
        $userModel->cnpj = '12345678901234';
        $userModel->password = 'hashed_password';
        $userModel->type = UserTypeEnum::SHOPKEEPER->value;

        $userEntity = $this->traitInstance->parseUserEntity($userModel);

        $this->assertEquals('shopkeeper-uuid', $userEntity->getId()->getValue());
        $this->assertEquals('Shopkeeper User', $userEntity->getName());
        $this->assertEquals('shopkeeper@example.com', $userEntity->getEmail());
        $this->assertNull($userEntity->getCpf());
        $this->assertEquals('12345678901234', $userEntity->getCnpj());
        $this->assertEquals(UserTypeEnum::SHOPKEEPER, $userEntity->getType());
    }
}
