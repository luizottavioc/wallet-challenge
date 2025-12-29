<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Adapter;

use App\Application\Contract\EventDispatcherInterface;
use App\Domain\Event\TransferCompletedEvent;
use App\Infrastructure\Adapter\EventDispatcherAdapterAmqp;
use App\Infrastructure\Amqp\Producer\TransferCompletedProducer;
use Hyperf\Amqp\Producer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function Hyperf\Config\config;

final class EventDispatcherAdapterAmqpTest extends TestCase
{
    private Producer|MockObject $producer;
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->producer = $this->createMock(Producer::class);
        $this->eventDispatcher = new EventDispatcherAdapterAmqp($this->producer);
    }

    public function testDispatchWithMappedEvent(): void
    {
        $event = new TransferCompletedEvent('transaction-uuid');

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->with($this->callback(function ($producer) use ($event) {
                return $producer instanceof TransferCompletedProducer;
            }));

        $this->eventDispatcher->dispatch($event);
    }

    public function testDispatchWithMultipleEventsConfig(): void
    {
        $event = new TransferCompletedEvent('transaction-uuid');

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->with($this->callback(function ($producer) use ($event) {
                return $producer instanceof TransferCompletedProducer;
            }));

        $this->eventDispatcher->dispatch($event);
    }

    public function testDispatchWithInvalidMappedEventClass(): void
    {
        $event = new TransferCompletedEvent('transaction-uuid');

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->willThrowException(new \Error("Class 'NonExistentClass' not found"));

        $this->expectException(\Error::class);

        $this->eventDispatcher->dispatch($event);
    }

    public function testDispatchWithProducerException(): void
    {
        $event = new TransferCompletedEvent('transaction-uuid');

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->willThrowException(new \RuntimeException('Producer error'));

        $this->expectException(\RuntimeException::class);

        $this->eventDispatcher->dispatch($event);
    }

    public function testDispatchWithDifferentEventTypes(): void
    {
        $transferEvent = new TransferCompletedEvent('transaction-uuid');

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->with($this->callback(function ($producer) use ($transferEvent) {
                return $producer instanceof TransferCompletedProducer;
            }));

        $this->eventDispatcher->dispatch($transferEvent);
    }

    public function testDispatchWithEventContainingSpecialCharacters(): void
    {
        $transactionId = 'transaction-uuid_with-special-chars!@#$%^&*()';
        $event = new TransferCompletedEvent($transactionId);

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->with($this->callback(function ($producer) use ($event) {
                return $producer instanceof TransferCompletedProducer;
            }));

        $this->eventDispatcher->dispatch($event);
    }

    public function testDispatchWithEmptyTransactionId(): void
    {
        $event = new TransferCompletedEvent('');

        $this->producer
            ->expects($this->once())
            ->method('produce')
            ->with($this->callback(function ($producer) use ($event) {
                return $producer instanceof TransferCompletedProducer;
            }));

        $this->eventDispatcher->dispatch($event);
    }
}
