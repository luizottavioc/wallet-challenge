<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\TransferAuthorizerInterface;
use App\Infrastructure\Enum\HttpCodesEnum;
use GuzzleHttp\ClientInterface;
use Throwable;

final readonly class TransferAuthorizerAdapterGuzzle implements TransferAuthorizerInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $endpoint
    ) {}

    public function authorize(): bool
    {
        try {
            $response = $this->client->get($this->endpoint);
            return $response->getStatusCode() === HttpCodesEnum::OK->value;
        } catch (Throwable $e) {
            return false;
        }
    }
}