<?php

declare(strict_types=1);

use App\Application\ErrorHandler\ErrorHandler;
use App\Application\Middleware\ErrorMiddleware;
use App\Config\ServiceRegistry;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $builder) {

    $builder->addDefinitions([
         'logger' => fn () => (require __DIR__ . '/logger.php')(),
        LoggerInterface::class => fn ($c) => $c->get('logger'),

        ErrorHandler::class => fn ($c) => new ErrorHandler($c->get('logger')),
        ErrorMiddleware::class => fn ($c) => new ErrorMiddleware($c->get(ErrorHandler::class)),

        ServiceRegistry::class => static fn () =>
        new ServiceRegistry([
            'auth' => $_ENV['AUTH_SERVICE_URL'],
            'group' => $_ENV['GROUP_SERVICE_URL'],
        ]),

    ]);
};
