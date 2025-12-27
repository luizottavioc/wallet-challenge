<?php

declare(strict_types=1);

namespace App\Infrastructure\Trait;

use App\Infrastructure\Enum\HttpCodesEnum;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;

trait HttpResponseTrait
{
    private readonly ResponseInterface $response;

    public function responseSuccess(
        HttpCodesEnum $statusCode,
        string $message,
        array $data = []
    ): ResponseInterface|PsrResponseInterface
    {
        return $this->response->withHeader('Content-Type', 'application/json')
            ->withBody(
                new SwooleStream(json_encode([
                    'message' => $message,
                    'data' => $data
                ]))
            )
            ->withStatus($statusCode->value);
    }

    public function responseError(
        HttpCodesEnum $statusCode,
        string $errorMessage,
        array $errors = [],
        ?ResponsePlusInterface $withCustomResponseClass = null
    ): ResponseInterface|PsrResponseInterface
    {
        $response = $withCustomResponseClass ?? $this->response;
        return $response->withHeader('Content-Type', 'application/json')
            ->withBody(
                new SwooleStream(json_encode([
                    'message' => $errorMessage,
                    'errors' => $errors
                ]))
            )
            ->withStatus($statusCode->value);
    }
}