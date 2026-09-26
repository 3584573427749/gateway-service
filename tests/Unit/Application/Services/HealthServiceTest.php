<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\HealthService;
use App\Config\ServiceRegistry;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class HealthServiceTest extends TestCase {
    public function testReturnsOkWhenAllServicesAreUp() : void {
        $stream = $this->createMock(StreamInterface::class);

        $stream
            ->method('getContents')
            ->willReturn(json_encode([
                'statusCode' => 200,
                'data' => [
                    'status' => 'ok',
                ],
            ]));

        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $response
            ->method('getBody')
            ->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects(self::exactly(2))
            ->method('request')
            ->willReturn($response);

        $service = new HealthService(
            $this->createMock(LoggerInterface::class),
            $client,
            new ServiceRegistry([
                'auth' => 'http://auth:8080',
                'groups' => 'http://group:8080',
            ]),
        );

        $health = $service->getHealthStatus();

        self::assertSame(
            'ok',
            $health['status'],
        );
    }

    public function testReturnsDegradedWhenServiceReturns500() : void {
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(500);

        $logger = $this->createMock(LoggerInterface::class);

        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $service = new HealthService(
            $logger,
            $client,
            $registry,
        );

        $health = $service->getHealthStatus();

        self::assertSame(
            'degraded',
            $health['status'],
        );

        self::assertSame(
            ['data' => ['status' => 'down', ], ],
            $health['services']['auth']['status'],
        );
    }

    public function testReturnsDegradedWhenExceptionOccurs() : void {
        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects(self::once())
            ->method('request')
            ->willThrowException(
                new RuntimeException('Connection refused'),
            );

        $logger = $this->createMock(LoggerInterface::class);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $service = new HealthService($logger, $client, $registry);

        $health = $service->getHealthStatus();

        self::assertSame(
            'degraded',
            $health['status'],
        );

        self::assertSame(
            ['data' => ['status' => 'down', ], ],
            $health['services']['auth']['status'],
        );
    }

    public function testReturnsGatewayServiceName() : void {
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $client = $this->createMock(ClientInterface::class);

        $client
            ->method('request')
            ->willReturn($response);

        $logger = $this->createMock(LoggerInterface::class);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $service = new HealthService($logger, $client, $registry);

        $health = $service->getHealthStatus();

        self::assertSame(
            'gateway-service',
            $health['service'],
        );
    }

    public function testReturnsVersion() : void {
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $client = $this->createMock(ClientInterface::class);

        $client
            ->method('request')
            ->willReturn($response);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $logger = $this->createMock(LoggerInterface::class);
        $service = new HealthService($logger, $client, $registry);

        $health = $service->getHealthStatus();

        self::assertArrayHasKey(
            'version',
            $health,
        );

        self::assertNotEmpty(
            $health['version'],
        );
    }

    public function testReturnsAllRegisteredServices() : void {
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $logger = $this->createMock(LoggerInterface::class);
        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects(self::exactly(2))
            ->method('request')
            ->willReturn($response);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
            'groups' => 'http://group:8080',
        ]);

        $service = new HealthService(
            $logger,
            $client,
            $registry,
        );

        $health = $service->getHealthStatus();

        self::assertArrayHasKey(
            'services',
            $health,
        );

        self::assertArrayHasKey(
            'auth',
            $health['services'],
        );

        self::assertArrayHasKey(
            'groups',
            $health['services'],
        );
    }
}
