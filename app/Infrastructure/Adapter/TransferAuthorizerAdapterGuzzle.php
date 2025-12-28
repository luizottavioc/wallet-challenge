<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\TransferAuthorizerInterface;
use App\Infrastructure\Enum\HttpCodesEnum;
use GuzzleHttp\Client;
use Throwable;
use function Hyperf\Config\config;

final class TransferAuthorizerAdapterGuzzle implements TransferAuthorizerInterface
{
    private Client $client;
    private string $authorizerTransferEndpoint;

    public function __construct()
    {
        $this->authorizerTransferEndpoint = config('consolidators.authorizer_transfer_endpoint');
        $this->client = new Client([
            'base_uri' => config('consolidators.authorizer_service_url')
        ]);
    }

    public function authorize(): bool
    {
        try {
            $response = $this->client->get($this->authorizerTransferEndpoint);
            return $response->getStatusCode() === HttpCodesEnum::OK->value;
        } catch (Throwable $e) {
            return false;
        }
    }
}