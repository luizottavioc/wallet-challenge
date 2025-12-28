<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}