<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\NotifierInterface;
use App\Application\DTO\NotificationDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function Hyperf\Config\config;

final class NotifierAdapterGuzzle implements NotifierInterface
{
    private Client $client;
    private string $authorizerTransferEndpoint;

    public function __construct()
    {
        $this->authorizerTransferEndpoint = config('consolidators.notifier_notify_endpoint');
        $this->client = new Client([
            'base_uri' => config('consolidators.notifier_service_url')
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function notify(NotificationDto $notifyDto): void
    {
        $this->client->post($this->authorizerTransferEndpoint, [
            'json' => $notifyDto->toArray()
        ]);
    }
}