<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class NotificationDto
{
    public function __construct(
        private string $emailTarget,
        private string $subject,
        private string $body,
    ) {}

    public function getEmailTarget(): string
    {
        return $this->emailTarget;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return [
            'emailTarget' => $this->getEmailTarget(),
            'subject' => $this->getSubject(),
            'body' => $this->getBody(),
        ];
    }
}