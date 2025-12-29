<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Adapter;

use App\Infrastructure\Adapter\TransferAuthorizerAdapterGuzzle;
use App\Infrastructure\Enum\HttpCodesEnum;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class TransferAuthorizerAdapterGuzzleTest extends TestCase
{
    private Client|MockObject $client;
    private TransferAuthorizerAdapterGuzzle $authorizer;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->authorizer = new TransferAuthorizerAdapterGuzzle($this->client, 'endpoint');
    }

    public function testAuthorizeSuccessfully(): void
    {
        $response = new Response(HttpCodesEnum::OK->value);

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $result = $this->authorizer->authorize();

        $this->assertTrue($result);
    }

    public function testAuthorizeWithUnauthorizedStatusCode(): void
    {
        $response = new Response(HttpCodesEnum::UNAUTHORIZED->value);

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithForbiddenStatusCode(): void
    {
        $response = new Response(HttpCodesEnum::FORBIDDEN->value);

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithInternalServerError(): void
    {
        $response = new Response(HttpCodesEnum::INTERNAL_SERVER_ERROR->value);

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithException(): void
    {
        $exception = new RuntimeException('Network error');

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithTimeoutException(): void
    {
        $exception = new ConnectException(
            'Timeout',
            new Request('GET', 'test')
        );

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithClientException(): void
    {
        $exception = new ClientException(
            'Client error',
            new Request('GET', 'test'),
            new Response(400)
        );

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithServerException(): void
    {
        $exception = new ServerException(
            'Server error',
            new Request('GET', 'test'),
            new Response(500)
        );

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithCreatedStatusCode(): void
    {
        $response = new Response(HttpCodesEnum::CREATED->value);

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }

    public function testAuthorizeWithAcceptedStatusCode(): void
    {
        $response = new Response(HttpCodesEnum::ACCEPTED->value);

        $this->client
            ->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $result = $this->authorizer->authorize();

        $this->assertFalse($result);
    }
}
