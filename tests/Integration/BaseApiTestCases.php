<?php

declare(strict_types=1);

namespace Tests\Integration;

use Cake\Core\ContainerInterface;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Tests\Integration\OpenApi\OpenApiValidator;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

abstract class BaseApiTestCases extends DatabaseBaseTestCase {
    /**
     * @var App<ContainerInterface>
     */
    protected App $app;

    protected function setUp() : void {
        parent::setUp();

        $dotenv = Dotenv::createImmutable(
            dirname(__DIR__, 2),
            '.env.testing',
        );

        $dotenv->load();

        $this->app = require __DIR__ . '/../../app/bootstrap.php';
    }

    protected function validateResponse(
        string $path,
        string $method,
        ResponseInterface $response,
    ) : void {
        $validator = new OpenApiValidator();

        $validator->validateResponse(
            $path,
            $method,
            $response,
        );
    }
}
