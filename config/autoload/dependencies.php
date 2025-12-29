<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Application\Contract\AuthenticatorInterface;
use App\Application\Contract\EventDispatcherInterface;
use App\Application\Contract\NotifierInterface;
use App\Application\Contract\TransactionManagerInterface;
use App\Application\Contract\TransferAuthorizerInterface;
use App\Domain\Contract\Repository\TransactionRepositoryInterface;
use App\Domain\Contract\Repository\UserRepositoryInterface;
use App\Domain\Contract\Repository\WalletRepositoryInterface;
use App\Domain\Contract\Service\PasswordHasherInterface;
use App\Infrastructure\Adapter\AuthenticatorAdapterJWT;
use App\Infrastructure\Adapter\EventDispatcherAdapterAmqp;
use App\Infrastructure\Adapter\NotifierAdapterGuzzle;
use App\Infrastructure\Adapter\PasswordHasherAdapterBcrypt;
use App\Infrastructure\Adapter\TransactionManagerAdapterHyperf;
use App\Infrastructure\Adapter\TransferAuthorizerAdapterGuzzle;
use App\Infrastructure\Repository\TransactionRepositoryEloquent;
use App\Infrastructure\Repository\UserRepositoryEloquent;
use App\Infrastructure\Repository\WalletRepositoryEloquent;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;

return [
    UserRepositoryInterface::class => UserRepositoryEloquent::class,
    AuthenticatorInterface::class => AuthenticatorAdapterJWT::class,
    PasswordHasherInterface::class => PasswordHasherAdapterBcrypt::class,
    TransactionManagerInterface::class => TransactionManagerAdapterHyperf::class,
    EventDispatcherInterface::class => EventDispatcherAdapterAmqp::class,
    WalletRepositoryInterface::class => WalletRepositoryEloquent::class,
    TransactionRepositoryInterface::class => TransactionRepositoryEloquent::class,
    TransferAuthorizerInterface::class => function ($container) {
        $factory = $container->get(ClientFactory::class);
        $config = $container->get(ConfigInterface::class);
        $client = $factory->create([
            'base_uri' => $config->get('consolidators.authorizer_service_url')
        ]);

        return new TransferAuthorizerAdapterGuzzle($client, $config->get('consolidators.authorizer_transfer_endpoint'));
    },
    NotifierInterface::class => function ($container) {
        $factory = $container->get(ClientFactory::class);
        $config = $container->get(ConfigInterface::class);
        $client = $factory->create([
            'base_uri' => $config->get('consolidators.notifier_notify_endpoint')
        ]);

        return new NotifierAdapterGuzzle($client, $config->get('consolidators.notifier_service_url'));
    }
];
