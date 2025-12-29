<?php

declare(strict_types=1);

namespace App\Infrastructure\Amqp\Consumer;

use App\Application\Contract\NotifierInterface;
use App\Application\DTO\NotificationDto;
use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

#[Consumer(
    exchange: 'transfer',
    routingKey: 'transfer.completed',
    queue: 'transfer',
    name: "TransferCompletedConsumer",
    nums: 1
)]
class TransferCompletedConsumer extends ConsumerMessage
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly NotifierInterface $notifier
    ) {}

    public function consumeMessage($data, AMQPMessage $message): Result
    {
        $transaction = $this->transactionRepository->findByTransactionId($data);
        if (is_null($transaction)) {
            return Result::ACK;
        }

        try {
            $this->notifier->notify(
                new NotificationDto(
                    emailTarget: $transaction->getPayee()->getEmail(),
                    subject: 'Transfer received',
                    body: 'You have received a transfer of ' . $transaction->getAmount()->format() . '.'
                )
            );

            return Result::ACK;
        } catch (Throwable $throwable) {
            return Result::REQUEUE;
        }
    }
}
