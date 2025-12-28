<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Random\RandomException;

readonly class Identifier
{
    private string $value;

    /**
     * @throws RandomException
     */
    public function __construct(?string $id = null)
    {
        $this->value = is_null($id) ? $this->generateUuid() : $id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @throws RandomException
     */
    private static function generateUuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}