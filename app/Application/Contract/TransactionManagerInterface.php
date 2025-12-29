<?php

declare(strict_types=1);

namespace App\Application\Contract;

use Closure;

interface TransactionManagerInterface
{
    /**
     * @template T
     * @param Closure(): T $callback
     * @return T
     */
    public function run(Closure $callback);
}