<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Adapter;

use App\Application\Contract\NotifierInterface;
use App\Application\DTO\NotificationDto;
use App\Infrastructure\Adapter\NotifierAdapterGuzzle;
use App\Infrastructure\Enum\HttpCodesEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class NotifierAdapterGuzzleTest extends TestCase
{
    private Client|MockObject $client;
    private NotifierInterface $notifier;
    private string $endpoint = 'https://api.notification.com/send';

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->notifier = new NotifierAdapterGuzzle($this->client, $this->endpoint);
    }

    public function testNotifySuccessfully(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                     'json' => $notificationDto->toArray()
                ]
            )
            ->willReturn(new Response(HttpCodesEnum::OK->value));

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithEmptySubject(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            '',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willReturn(new Response(HttpCodesEnum::OK->value));

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithEmptyBody(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            ''
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willReturn(new Response(HttpCodesEnum::OK->value));

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithClientException(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willThrowException($this->createMock(GuzzleException::class));

        $this->expectException(GuzzleException::class);

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithNetworkError(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willThrowException(new RuntimeException('Network error'));

        $this->expectException(RuntimeException::class);

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithTimeout(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willThrowException(new ConnectException(
                'Timeout',
                new Request('POST', $this->endpoint)
            ));

        $this->expectException(ConnectException::class);

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithServerError(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willThrowException(new \GuzzleHttp\Exception\ServerException(
                'Server error',
                new Request('POST', $this->endpoint),
                new Response(500)
            ));

        $this->expectException(\GuzzleHttp\Exception\ServerException::class);

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithClientError(): void
    {
        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->endpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willThrowException(new ClientException(
                'Client error',
                new Request('POST', $this->endpoint),
                new Response(400)
            ));

        $this->expectException(ClientException::class);

        $this->notifier->notify($notificationDto);
    }

    public function testNotifyWithDifferentEndpoint(): void
    {
        $customEndpoint = 'https://custom.api.notification.com/notify';
        $notifier = new NotifierAdapterGuzzle($this->client, $customEndpoint);

        $notificationDto = new NotificationDto(
            'test@example.com',
            'Test Subject',
            'Test Body'
        );

        $this->client
            ->expects($this->once())
            ->method('post')
            ->with(
                $customEndpoint,
                [
                    'json' => $notificationDto->toArray()
                ]
            )
            ->willReturn(new Response(HttpCodesEnum::OK->value));

        $notifier->notify($notificationDto);
    }
}
