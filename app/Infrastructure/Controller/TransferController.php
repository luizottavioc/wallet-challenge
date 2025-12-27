<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class TransferController extends AbstractController
{
    public function __construct(
        RequestInterface $request,
        ResponseInterface $response,
    ) {
        parent::__construct($request, $response);
    }

    public function transfer()
    {
        return 1;
    }
}
