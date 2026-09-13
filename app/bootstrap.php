<?php

declare(strict_types=1);


use App\Application\Middleware\ErrorMiddleware;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(
    dirname(__DIR__),
);

$dotenv->load();
$containerBuilder = new ContainerBuilder();

(require __DIR__ . '/dependencies.php')($containerBuilder);

$repositories = require __DIR__ . '/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(ErrorMiddleware::class);

(require __DIR__ . '/middleware.php')($app);
(require __DIR__ . '/routes.php')($app);

return $app;
