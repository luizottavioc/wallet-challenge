<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\NotifierInterface;
use App\Application\DTO\NotificationDto;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

final readonly class NotifierAdapterGuzzle implements NotifierInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $endpoint
    ) {}

    /**
     * @throws GuzzleException
     */
    public function notify(NotificationDto $notifyDto): void
    {
        $this->client->post($this->endpoint, [
            'json' => $notifyDto->toArray()
        ]);
    }
}