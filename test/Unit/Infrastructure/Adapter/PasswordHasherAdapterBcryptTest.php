<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Adapter;

use App\Infrastructure\Adapter\PasswordHasherAdapterBcrypt;
use PHPUnit\Framework\TestCase;

final class PasswordHasherAdapterBcryptTest extends TestCase
{
    private PasswordHasherAdapterBcrypt $passwordHasher;

    protected function setUp(): void
    {
        $this->passwordHasher = new PasswordHasherAdapterBcrypt();
    }

    public function testHashGeneratesValidHash(): void
    {
        $plainPassword = 'password123';

        $hash = $this->passwordHasher->hash($plainPassword);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($plainPassword, $hash);
    }

    public function testHashGeneratesDifferentHashesForSamePassword(): void
    {
        $plainPassword = 'password123';

        $hash1 = $this->passwordHasher->hash($plainPassword);
        $hash2 = $this->passwordHasher->hash($plainPassword);

        $this->assertNotEquals($hash1, $hash2);
    }

    public function testHashWithEmptyPassword(): void
    {
        $plainPassword = '';

        $hash = $this->passwordHasher->hash($plainPassword);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testHashWithLongPassword(): void
    {
        $plainPassword = str_repeat('a', 1000);

        $hash = $this->passwordHasher->hash($plainPassword);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testHashWithSpecialCharacters(): void
    {
        $plainPassword = 'P@$$w0rd!#$%^&*()_+-=[]{}|;:,.<>?';

        $hash = $this->passwordHasher->hash($plainPassword);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testCheckWithValidPassword(): void
    {
        $plainPassword = 'password123';

        $hash = $this->passwordHasher->hash($plainPassword);
        $result = $this->passwordHasher->check($plainPassword, $hash);

        $this->assertTrue($result);
    }

    public function testCheckWithInvalidPassword(): void
    {
        $plainPassword = 'password123';
        $wrongPassword = 'wrongpassword';

        $hash = $this->passwordHasher->hash($plainPassword);
        $result = $this->passwordHasher->check($wrongPassword, $hash);

        $this->assertFalse($result);
    }

    public function testCheckWithEmptyPassword(): void
    {
        $plainPassword = '';

        $hash = $this->passwordHasher->hash($plainPassword);
        $result = $this->passwordHasher->check($plainPassword, $hash);

        $this->assertTrue($result);
    }

    public function testCheckWithNonEmptyPasswordAgainstEmptyHash(): void
    {
        $plainPassword = 'password123';
        $emptyHash = '';

        $result = $this->passwordHasher->check($plainPassword, $emptyHash);

        $this->assertFalse($result);
    }


    public function testCheckWithInvalidHash(): void
    {
        $plainPassword = 'password123';
        $invalidHash = 'invalid_hash_string';

        $result = $this->passwordHasher->check($plainPassword, $invalidHash);

        $this->assertFalse($result);
    }
}
