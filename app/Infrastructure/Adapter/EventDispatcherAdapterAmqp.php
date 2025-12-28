<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\EventDispatcherInterface;

final class EventDispatcherAdapterAmqp implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        // TODO: Implement dispatch() method.
    }

}