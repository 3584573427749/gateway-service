<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\ProxyService;
use App\Config\ServiceRegistry;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

final class ProxyServiceTest extends TestCase {
    /**
     * @param array<string, array<int, string>> $headers
     * @throws Exception
     */
    private function createRequestMock(
        string $method,
        array $headers = [],
        string $body = '',
    ) : ServerRequestInterface {
        $request = $this->createMock(ServerRequestInterface::class);

        $request
            ->method('getMethod')
            ->willReturn($method);

        $request
            ->method('getHeaders')
            ->willReturn($headers);

        $stream = $this->createMock(
            \Psr\Http\Message\StreamInterface::class,
        );

        $stream
            ->method('__toString')
            ->willReturn($body);

        $request
            ->method('getBody')
            ->willReturn($stream);

        return $request;
    }

    public function testForwardsGetRequestToAuthService() : void {
        $request = $this->createRequestMock('GET');

        $response = $this->createMock(ResponseInterface::class);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
            'group' => 'http://group:8080',
        ]);

        $httpClient = $this->createMock(ClientInterface::class);

        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                'http://auth:8080/users',
                self::anything(),
            )
            ->willReturn($response);

        $service = new ProxyService(
            $httpClient,
            $registry,
        );

        $result = $service->forward(
            $request,
            'auth',
            'users',
        );

        self::assertSame($response, $result);
    }

    public function testForwardsPostRequestToGroupService() : void {
        $request = $this->createRequestMock(
            'POST',
            [],
            '{"name":"Test"}',
        );

        $response = $this->createMock(ResponseInterface::class);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
            'group' => 'http://group:8080',
        ]);

        $httpClient = $this->createMock(ClientInterface::class);

        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                'http://group:8080/groups',
                self::callback(
                    static function (array $options) : bool {
                        return $options['body']
                            === '{"name":"Test"}';
                    },
                ),
            )
            ->willReturn($response);

        $service = new ProxyService(
            $httpClient,
            $registry,
        );

        $service->forward(
            $request,
            'group',
            'groups',
        );
    }

    public function testForwardsHeaders() : void {
        $headers = [
            'Authorization' => [
                'Bearer token',
            ],
            'Content-Type' => [
                'application/json',
            ],
        ];

        $request = $this->createRequestMock(
            'GET',
            $headers,
        );

        $response = $this->createMock(ResponseInterface::class);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $httpClient = $this->createMock(ClientInterface::class);

        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                self::anything(),
                self::anything(),
                self::callback(
                    static function (array $options) : bool {
                        return isset(
                            $options['headers']['Authorization'],
                        );
                    },
                ),
            )
            ->willReturn($response);

        $service = new ProxyService(
            $httpClient,
            $registry,
        );

        $service->forward(
            $request,
            'auth',
            'users',
        );
    }

    public function testRemovesHostHeader() : void {
        $request = $this->createRequestMock(
            'GET',
            [
                'Host' => ['gateway:8080'],
            ],
        );

        $response = $this->createMock(ResponseInterface::class);

        $registry = new ServiceRegistry([
            'auth' => 'http://auth:8080',
        ]);

        $httpClient = $this->createMock(ClientInterface::class);

        $httpClient
            ->expects(self::once())
            ->method('request')
            ->with(
                self::anything(),
                self::anything(),
                self::callback(
                    static function (array $options) : bool {
                        return !isset(
                            $options['headers']['Host'],
                        );
                    },
                ),
            )
            ->willReturn($response);

        $service = new ProxyService(
            $httpClient,
            $registry,
        );

        $service->forward(
            $request,
            'auth',
            'users',
        );
    }

    public function testThrowsExceptionForUnknownService() : void {
        $request = $this->createRequestMock('GET');

        $registry = new ServiceRegistry([]);

        $httpClient = $this->createMock(ClientInterface::class);

        $service = new ProxyService(
            $httpClient,
            $registry,
        );

        $this->expectException(
            RuntimeException::class,
        );

        $service->forward(
            $request,
            'unknown',
            'users',
        );
    }
}
