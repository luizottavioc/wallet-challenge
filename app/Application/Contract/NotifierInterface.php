<?php

declare(strict_types=1);

namespace App\Application\Contract;

use App\Application\DTO\NotificationDto;

interface NotifierInterface
{
    public function notify(NotificationDto $notifyDto): void;
}