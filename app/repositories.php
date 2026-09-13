<?php

declare(strict_types=1);

use App\Domain\Repositories\UserRepository;
use App\Infrastructure\Database\User\DbalUserRepository;

use function DI\autowire;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Repository-mappningar
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(DbalUserRepository::class),
    ]);
};
