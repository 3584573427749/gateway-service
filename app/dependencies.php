<?php

declare(strict_types=1);

use App\Application\ErrorHandler\ErrorHandler;
use App\Application\Middleware\ErrorMiddleware;
use App\Config\ServiceRegistry;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $builder) : void {
    $builder->addDefinitions([

        'logger' => fn () => (require __DIR__ . '/logger.php')(),

        LoggerInterface::class => fn ($c) => $c->get('logger'),

        ErrorHandler::class => fn ($c) => new ErrorHandler(
            $c->get(LoggerInterface::class),
        ),

        ErrorMiddleware::class => fn ($c) => new ErrorMiddleware(
            $c->get(ErrorHandler::class),
        ),

        ClientInterface::class => static fn () => new Client([
            'timeout' => (float) ($_ENV['REQUEST_TIMEOUT'] ?? 5),
        ]),

        ServiceRegistry::class => static fn () => new ServiceRegistry([
            'auth' => $_ENV['AUTH_SERVICE_URL'],
            'groups' => $_ENV['GROUP_SERVICE_URL'],
        ]),
    ]);
};
