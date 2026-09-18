<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\HealthService;
use App\Config\ServiceRegistry;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class HealthServiceTest extends TestCase {
    public function testReturnsHealthyWhenAllServicesAreUp() : void {
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(200);

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
            $client,
            $registry,
        );

        self::assertSame(
            [
                'status' => 'healthy',
                'services' => [
                    'auth' => [
                        'status' => 'up',
                    ],
                    'groups' => [
                        'status' => 'up',
                    ],
                ],
            ],
            $service->getHealthStatus(),
        );
    }

    public function testReturnsDegradedWhenAServiceReturns500() : void {
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getStatusCode')
            ->willReturn(500);

        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $service = new HealthService(
            $client,
            $registry,
        );

        self::assertSame(
            [
                'status' => 'degraded',
                'services' => [
                    'auth' => [
                        'status' => 'down',
                    ],
                ],
            ],
            $service->getHealthStatus(),
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

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $service = new HealthService(
            $client,
            $registry,
        );

        self::assertSame(
            [
                'status' => 'degraded',
                'services' => [
                    'auth' => [
                        'status' => 'down',
                    ],
                ],
            ],
            $service->getHealthStatus(),
        );
    }
}
