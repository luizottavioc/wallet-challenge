<?php

declare(strict_types=1);

namespace App\Infrastructure\Trait;

use App\Infrastructure\Enum\HttpCodesEnum;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait HttpResponseTrait
{
    private RequestInterface $request;
    private ResponseInterface $response;

    public function responseSuccess(
        HttpCodesEnum $statusCode,
        string $message,
        array $data = []
    ): ResponseInterface
    {
        return $this->response->json([
            'message' => $message,
            'data' => $data
        ])
            ->withStatus($statusCode->value);
    }

    public function responseError(
        HttpCodesEnum $statusCode,
        string $errorMessage
    ): ResponseInterface
    {
        return $this->response->json(['message' => $errorMessage])
            ->withStatus($statusCode->value);
    }
}