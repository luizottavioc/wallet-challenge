<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Infrastructure\Trait\HttpResponseTrait;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

abstract class AbstractController
{
    use HttpResponseTrait;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly ResponseInterface $response
    ) {}
}
