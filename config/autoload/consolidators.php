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

use function Hyperf\Support\env;

return [
    'authorizer_service_url' => env('AUTHORIZER_SERVICE_URL', 'https://util.devi.tools'),
    'authorizer_transfer_endpoint' => env('AUTHORIZER_TRANSFER_ENDPOINT', '/api/v2/authorize'),
    'notifier_service_url' => env('NOTIFIER_SERVICE_URL', 'https://util.devi.tools'),
    'notifier_notify_endpoint' => env('NOTIFIER_NOTIFY_ENDPOINT', '/api/v1/notify'),
];
