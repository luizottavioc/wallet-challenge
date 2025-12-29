<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Domain\Event;

use App\Domain\Event\TransferCompletedEvent;
use PHPUnit\Framework\TestCase;

final class TransferCompletedEventTest extends TestCase
{
    public function testTransferCompletedEventCreation(): void
    {
        $transactionId = 'transaction-uuid';

        $event = new TransferCompletedEvent($transactionId);

        $this->assertEquals($transactionId, $event->transactionId);
    }
}
