<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\TransferAuthorizerInterface;

final class TransferAuthorizerAdapterGuzzle implements TransferAuthorizerInterface
{
    public function authorize(): bool
    {
        return true;
    }
}