<?php

declare(strict_types=1);

namespace Tests\Integration;

use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

abstract class BaseApiTestCases {
    protected App $app;

    protected function setUp() : void {

        $dotenv = Dotenv::createImmutable(
            dirname(__DIR__, 2),
            '.env.testing',
        );

        $dotenv->load();

        $this->app = require __DIR__ . '/../../app/bootstrap.php';
    }
}
