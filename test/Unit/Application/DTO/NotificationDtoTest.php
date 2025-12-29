<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Application\DTO;

use App\Application\DTO\NotificationDto;
use PHPUnit\Framework\TestCase;

final class NotificationDtoTest extends TestCase
{
    public function testNotificationDtoCreation(): void
    {
        $emailTarget = 'test@example.com';
        $subject = 'Test Subject';
        $body = 'Test Body';

        $notificationDto = new NotificationDto($emailTarget, $subject, $body);

        $this->assertEquals($emailTarget, $notificationDto->getEmailTarget());
        $this->assertEquals($subject, $notificationDto->getSubject());
        $this->assertEquals($body, $notificationDto->getBody());
    }

    public function testNotificationDtoToArray(): void
    {
        $emailTarget = 'test@example.com';
        $subject = 'Test Subject';
        $body = 'Test Body';

        $notificationDto = new NotificationDto($emailTarget, $subject, $body);
        $result = $notificationDto->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('emailTarget', $result);
        $this->assertArrayHasKey('subject', $result);
        $this->assertArrayHasKey('body', $result);

        $this->assertEquals($emailTarget, $result['emailTarget']);
        $this->assertEquals($subject, $result['subject']);
        $this->assertEquals($body, $result['body']);
    }

    public function testNotificationDtoWithEmptyEmail(): void
    {
        $emailTarget = '';
        $subject = 'Test Subject';
        $body = 'Test Body';

        $notificationDto = new NotificationDto($emailTarget, $subject, $body);

        $this->assertEquals($emailTarget, $notificationDto->getEmailTarget());
        $this->assertEquals($subject, $notificationDto->getSubject());
        $this->assertEquals($body, $notificationDto->getBody());
    }

    public function testNotificationDtoWithEmptySubject(): void
    {
        $emailTarget = 'test@example.com';
        $subject = '';
        $body = 'Test Body';

        $notificationDto = new NotificationDto($emailTarget, $subject, $body);

        $this->assertEquals($emailTarget, $notificationDto->getEmailTarget());
        $this->assertEquals($subject, $notificationDto->getSubject());
        $this->assertEquals($body, $notificationDto->getBody());
    }

    public function testNotificationDtoWithEmptyBody(): void
    {
        $emailTarget = 'test@example.com';
        $subject = 'Test Subject';
        $body = '';

        $notificationDto = new NotificationDto($emailTarget, $subject, $body);

        $this->assertEquals($emailTarget, $notificationDto->getEmailTarget());
        $this->assertEquals($subject, $notificationDto->getSubject());
        $this->assertEquals($body, $notificationDto->getBody());
    }
}
