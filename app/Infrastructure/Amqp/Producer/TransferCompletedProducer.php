<?php

declare(strict_types=1);

namespace App\Infrastructure\Amqp\Producer;

use App\Domain\Event\TransferCompletedEvent;
use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'transfer', routingKey: 'transfer.completed')]
class TransferCompletedProducer extends ProducerMessage
{
    public function __construct(TransferCompletedEvent $transferCompletedEvent)
    {
        $this->payload = $transferCompletedEvent->transactionId;
    }
}
