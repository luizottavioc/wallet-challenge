<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Application\Contract\TransactionManagerInterface;
use Closure;
use Hyperf\DbConnection\Db;

final class TransactionManagerAdapterHyperf implements TransactionManagerInterface
{
    public function run(Closure $callback): mixed
    {
        return Db::transaction($callback);
    }
}