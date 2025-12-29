<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\EventDispatcherInterface;
use Hyperf\Amqp\Producer;
use function Hyperf\Config\config;

final readonly class EventDispatcherAdapterAmqp implements EventDispatcherInterface
{
    public function __construct(
        private Producer $producer
    ) {}

    public function dispatch(object $event): void
    {
        $eventsMap = config('events', []);
        $mappedEvent = $eventsMap[get_class($event)] ?? null;

        if (!$mappedEvent) {
            return;
        }

        $this->producer->produce(new $mappedEvent($event));
    }

}