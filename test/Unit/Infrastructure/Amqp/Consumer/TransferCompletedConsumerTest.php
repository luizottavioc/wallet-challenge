<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Infrastructure\Amqp\Consumer;

use App\Application\Contract\NotifierInterface;
use App\Application\DTO\NotificationDto;
use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use App\Domain\Entity\TransactionEntity;
use App\Domain\Entity\UserEntity;
use App\Domain\ValueObject\Identifier;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\PrecisionTimestamp;
use App\Domain\Enum\UserTypeEnum;
use App\Infrastructure\Amqp\Consumer\TransferCompletedConsumer;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TransferCompletedConsumerTest extends TestCase
{
    private TransactionRepositoryInterface|MockObject $transactionRepository;
    private NotifierInterface|MockObject $notifier;
    private TransferCompletedConsumer $consumer;

    protected function setUp(): void
    {
        $this->transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $this->notifier = $this->createMock(NotifierInterface::class);
        $this->consumer = new TransferCompletedConsumer(
            $this->transactionRepository,
            $this->notifier
        );
    }

    public function testConsumeMessageWithValidTransaction(): void
    {
        $transactionId = 'transaction-uuid';
        $payeeEmail = 'payee@example.com';
        $amount = 10000;

        $payeeUser = new UserEntity(
            id: new Identifier('payee-uuid'),
            name: 'Payee User',
            email: $payeeEmail,
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: new UserEntity(
                id: new Identifier('payer-uuid'),
                name: 'Payer User',
                email: 'payer@example.com',
                cpf: '12345678901',
                cnpj: null,
                password: 'hashed_password',
                type: UserTypeEnum::DEFAULT
            ),
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn($transaction);

        $this->notifier
            ->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (NotificationDto $notification) use ($payeeEmail, $amount) {
                return $notification->getEmailTarget() === $payeeEmail &&
                       $notification->getSubject() === 'Transfer received' &&
                       str_contains($notification->getBody(), '$ 100.00');
            }));

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::ACK, $result);
    }

    public function testConsumeMessageWithNullTransaction(): void
    {
        $transactionId = 'non-existent-transaction';

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn(null);

        $this->notifier
            ->expects($this->never())
            ->method('notify');

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::ACK, $result);
    }

    public function testConsumeMessageWithNotifierException(): void
    {
        $transactionId = 'transaction-uuid';
        $payeeEmail = 'payee@example.com';
        $amount = 10000;

        $payeeUser = new UserEntity(
            id: new Identifier('payee-uuid'),
            name: 'Payee User',
            email: $payeeEmail,
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: new UserEntity(
                id: new Identifier('payer-uuid'),
                name: 'Payer User',
                email: 'payer@example.com',
                cpf: '12345678901',
                cnpj: null,
                password: 'hashed_password',
                type: UserTypeEnum::DEFAULT
            ),
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn($transaction);

        $this->notifier
            ->expects($this->once())
            ->method('notify')
            ->willThrowException(new \RuntimeException('Notification error'));

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::REQUEUE, $result);
    }

    public function testConsumeMessageWithZeroAmount(): void
    {
        $transactionId = 'transaction-uuid';
        $payeeEmail = 'payee@example.com';
        $amount = 0;

        $payeeUser = new UserEntity(
            id: new Identifier('payee-uuid'),
            name: 'Payee User',
            email: $payeeEmail,
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: new UserEntity(
                id: new Identifier('payer-uuid'),
                name: 'Payer User',
                email: 'payer@example.com',
                cpf: '12345678901',
                cnpj: null,
                password: 'hashed_password',
                type: UserTypeEnum::DEFAULT
            ),
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn($transaction);

        $this->notifier
            ->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (NotificationDto $notification) use ($payeeEmail, $amount) {
                return $notification->getEmailTarget() === $payeeEmail &&
                       $notification->getSubject() === 'Transfer received' &&
                       str_contains($notification->getBody(), '$ 0.00');
            }));

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::ACK, $result);
    }

    public function testConsumeMessageWithLargeAmount(): void
    {
        $transactionId = 'transaction-uuid';
        $payeeEmail = 'payee@example.com';
        $amount = 999999999;

        $payeeUser = new UserEntity(
            id: new Identifier('payee-uuid'),
            name: 'Payee User',
            email: $payeeEmail,
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: new UserEntity(
                id: new Identifier('payer-uuid'),
                name: 'Payer User',
                email: 'payer@example.com',
                cpf: '12345678901',
                cnpj: null,
                password: 'hashed_password',
                type: UserTypeEnum::DEFAULT
            ),
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn($transaction);

        $this->notifier
            ->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (NotificationDto $notification) use ($payeeEmail, $amount) {
                return $notification->getEmailTarget() === $payeeEmail &&
                       $notification->getSubject() === 'Transfer received' &&
                       str_contains($notification->getBody(), '$ 9,999,999.99');
            }));

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::ACK, $result);
    }

    public function testConsumeMessageWithShopkeeperPayee(): void
    {
        $transactionId = 'transaction-uuid';
        $payeeEmail = 'shopkeeper@example.com';
        $amount = 10000;

        $payeeUser = new UserEntity(
            id: new Identifier('payee-uuid'),
            name: 'Shopkeeper User',
            email: $payeeEmail,
            cpf: null,
            cnpj: '12345678901234',
            password: 'hashed_password',
            type: UserTypeEnum::SHOPKEEPER
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: new UserEntity(
                id: new Identifier('payer-uuid'),
                name: 'Payer User',
                email: 'payer@example.com',
                cpf: '12345678901',
                cnpj: null,
                password: 'hashed_password',
                type: UserTypeEnum::DEFAULT
            ),
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn($transaction);

        $this->notifier
            ->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (NotificationDto $notification) use ($payeeEmail, $amount) {
                return $notification->getEmailTarget() === $payeeEmail &&
                       $notification->getSubject() === 'Transfer received' &&
                       str_contains($notification->getBody(), '$ 100.00');
            }));

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::ACK, $result);
    }

    public function testConsumeMessageWithThrowableException(): void
    {
        $transactionId = 'transaction-uuid';
        $payeeEmail = 'payee@example.com';
        $amount = 10000;

        $payeeUser = new UserEntity(
            id: new Identifier('payee-uuid'),
            name: 'Payee User',
            email: $payeeEmail,
            cpf: '98765432109',
            cnpj: null,
            password: 'hashed_password',
            type: UserTypeEnum::DEFAULT
        );

        $transaction = new TransactionEntity(
            id: new Identifier($transactionId),
            payer: new UserEntity(
                id: new Identifier('payer-uuid'),
                name: 'Payer User',
                email: 'payer@example.com',
                cpf: '12345678901',
                cnpj: null,
                password: 'hashed_password',
                type: UserTypeEnum::DEFAULT
            ),
            payee: $payeeUser,
            amount: new Money($amount),
            processedAt: new PrecisionTimestamp()
        );

        $this->transactionRepository
            ->expects($this->once())
            ->method('findByTransactionId')
            ->with($transactionId)
            ->willReturn($transaction);

        $this->notifier
            ->expects($this->once())
            ->method('notify')
            ->willThrowException(new \Error('Fatal error'));

        $message = $this->createMock(AMQPMessage::class);
        $result = $this->consumer->consumeMessage($transactionId, $message);

        $this->assertEquals(Result::REQUEUE, $result);
    }
}
