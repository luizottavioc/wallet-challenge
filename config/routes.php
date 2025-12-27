<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Infrastructure\Controller\AuthController;
use App\Infrastructure\Controller\TransferController;
use App\Infrastructure\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;
use Hyperf\Validation\Middleware\ValidationMiddleware;

Router::get('/ping', fn() => 'pong');

Router::addGroup('/auth', function () {
    Router::post('/login', [AuthController::class, 'login']);
}, [
    'middleware' => [ValidationMiddleware::class]
]);

Router::addGroup('/transfer', function () {
    Router::post('', [TransferController::class, 'transfer']);
}, [
    'middleware' => [AuthMiddleware::class, ValidationMiddleware::class]
]);

