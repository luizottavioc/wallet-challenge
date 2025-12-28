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
    'transfer_authorizer_endpoint' => env('TRANSFER_AUTHORIZER_ENDPOINT', 'https://util.devi.tools/api/v2/authorize'),
    'notify_endpoint' => env('NOTIFY_ENDPOINT', 'https://util.devi.tools/api/v1/notify'),
];
