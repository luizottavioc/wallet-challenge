<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Adapter;

use App\Application\Contract\EventDispatcherInterface;
use App\Infrastructure\Adapter\EventDispatcherAdapterAmqp;
use Hyperf\Amqp\Producer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EventDispatcherAdapterAmqpTest extends TestCase
{
    private Producer|MockObject $producer;
    private EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->producer = $this->createMock(Producer::class);
        $this->eventDispatcher = new EventDispatcherAdapterAmqp($this->producer);
    }

//    public function testDispatchWithMappedEvent(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//        $mappedEventClass = 'App\\Infrastructure\\Amqp\\Producer\\TransferCompletedProducer';
//
//        config(['events' => [
//            TransferCompletedEvent::class => $mappedEventClass
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->with($this->callback(function ($producer) use ($event) {
//                return $producer instanceof TransferCompletedProducer && $producer->event === $event;
//            }));
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithUnmappedEvent(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//
//        config(['events' => []]);
//
//        $this->producer
//            ->expects($this->never())
//            ->method('produce');
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithNullMappedEvent(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//
//        config(['events' => [
//            TransferCompletedEvent::class => null
//        ]]);
//
//        $this->producer
//            ->expects($this->never())
//            ->method('produce');
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithEmptyEventsConfig(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//
//        config(['events' => []]);
//
//        $this->producer
//            ->expects($this->never())
//            ->method('produce');
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithMultipleEventsConfig(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//        $mappedEventClass = 'App\\Infrastructure\\Amqp\\Producer\\TransferCompletedProducer';
//
//        config(['events' => [
//            'SomeOtherEvent' => 'SomeOtherProducer',
//            TransferCompletedEvent::class => $mappedEventClass,
//            'AnotherEvent' => 'AnotherProducer'
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->with($this->callback(function ($producer) use ($event) {
//                return $producer instanceof $mappedEventClass && $producer->event === $event;
//            }));
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithInvalidMappedEventClass(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//        $invalidEventClass = 'NonExistentClass';
//
//        config(['events' => [
//            TransferCompletedEvent::class => $invalidEventClass
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->willThrowException(new \Error("Class '$invalidEventClass' not found"));
//
//        $this->expectException(\Error::class);
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithProducerException(): void
//    {
//        $event = new TransferCompletedEvent('transaction-uuid');
//        $mappedEventClass = 'App\\Infrastructure\\Amqp\\Producer\\TransferCompletedProducer';
//
//        config(['events' => [
//            TransferCompletedEvent::class => $mappedEventClass
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->willThrowException(new \RuntimeException('Producer error'));
//
//        $this->expectException(\RuntimeException::class);
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithDifferentEventTypes(): void
//    {
//        $transferEvent = new TransferCompletedEvent('transaction-uuid');
//        $mappedEventClass = 'App\\Infrastructure\\Amqp\\Producer\\TransferCompletedProducer';
//
//        config(['events' => [
//            TransferCompletedEvent::class => $mappedEventClass
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->with($this->callback(function ($producer) use ($transferEvent) {
//                return $producer instanceof $mappedEventClass && $producer->event === $transferEvent;
//            }));
//
//        $this->eventDispatcher->dispatch($transferEvent);
//    }
//
//    public function testDispatchWithEventContainingSpecialCharacters(): void
//    {
//        $transactionId = 'transaction-uuid_with-special-chars!@#$%^&*()';
//        $event = new TransferCompletedEvent($transactionId);
//        $mappedEventClass = 'App\\Infrastructure\\Amqp\\Producer\\TransferCompletedProducer';
//
//        config(['events' => [
//            TransferCompletedEvent::class => $mappedEventClass
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->with($this->callback(function ($producer) use ($event) {
//                return $producer instanceof $mappedEventClass && $producer->event === $event;
//            }));
//
//        $this->eventDispatcher->dispatch($event);
//    }
//
//    public function testDispatchWithEmptyTransactionId(): void
//    {
//        $event = new TransferCompletedEvent('');
//        $mappedEventClass = 'App\\Infrastructure\\Amqp\\Producer\\TransferCompletedProducer';
//
//        config(['events' => [
//            TransferCompletedEvent::class => $mappedEventClass
//        ]]);
//
//        $this->producer
//            ->expects($this->once())
//            ->method('produce')
//            ->with($this->callback(function ($producer) use ($event) {
//                return $producer instanceof $mappedEventClass && $producer->event === $event;
//            }));
//
//        $this->eventDispatcher->dispatch($event);
//    }
}
