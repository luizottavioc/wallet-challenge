<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Amqp\Producer;

use App\Domain\Event\TransferCompletedEvent;
use App\Infrastructure\Amqp\Producer\TransferCompletedProducer;
use PHPUnit\Framework\TestCase;

final class TransferCompletedProducerTest extends TestCase
{
    public function testProducerCreationWithValidEvent(): void
    {
        $transactionId = 'transaction-uuid';
        $event = new TransferCompletedEvent($transactionId);

        $producer = new TransferCompletedProducer($event);

        $this->assertEquals($transactionId, $producer->getPayload());
    }

    public function testProducerCreationWithEmptyTransactionId(): void
    {
        $transactionId = '';
        $event = new TransferCompletedEvent($transactionId);

        $producer = new TransferCompletedProducer($event);

        $this->assertEquals($transactionId, $producer->getPayload());
    }

    public function testProducerCreationWithDifferentEvents(): void
    {
        $transactionId1 = 'transaction-1';
        $transactionId2 = 'transaction-2';
        
        $event1 = new TransferCompletedEvent($transactionId1);
        $event2 = new TransferCompletedEvent($transactionId2);

        $producer1 = new TransferCompletedProducer($event1);
        $producer2 = new TransferCompletedProducer($event2);

        $this->assertEquals($transactionId1, $producer1->getPayload());
        $this->assertEquals($transactionId2, $producer2->getPayload());
        $this->assertNotEquals($producer1->getPayload(), $producer2->getPayload());
    }

    public function testProducerPayloadIsString(): void
    {
        $transactionId = 'transaction-uuid';
        $event = new TransferCompletedEvent($transactionId);

        $producer = new TransferCompletedProducer($event);

        $this->assertIsString($producer->getPayload());
        $this->assertEquals($transactionId, $producer->getPayload());
    }
}
