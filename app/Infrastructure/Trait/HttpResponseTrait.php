<?php

declare(strict_types=1);

namespace App\Infrastructure\Trait;

use App\Infrastructure\Enum\HttpCodesEnum;
use Psr\Http\Message\ResponseInterface;

trait HttpResponseTrait
{
    public function responseSuccess(
        ResponseInterface $response,
        HttpCodesEnum $statusCode,
        string $message,
        array $data = []
    ): ResponseInterface
    {
        return $response->json([
            'message' => $message,
            'data' => $data
        ])
            ->withStatus($statusCode->value);
    }

    public function responseError(
        ResponseInterface $response,
        HttpCodesEnum $statusCode,
        string $errorMessage
    ): ResponseInterface
    {
        return $response->json(['message' => $errorMessage])
            ->withStatus($statusCode->value);
    }
}