<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\Proxy;

use App\Application\Services\ProxyService;
use App\Http\Actions\Proxy\ProxyAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class ProxyActionTest extends TestCase {
    public function testRoutesAuthRequestCorrectly() : void {
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $proxyResponse = $this->createMock(ResponseInterface::class);

        $proxyService = $this->createMock(ProxyService::class);

        $proxyService
            ->expects(self::once())
            ->method('forward')
            ->with(
                $request,
                'auth',
                'users',
            )
            ->willReturn($proxyResponse);

        $action = new ProxyAction(
            $this->createMock(LoggerInterface::class),
            $proxyService,
        );

        $result = $action(
            $request,
            $response,
            [
                'service' => 'auth',
                'path' => 'users',
            ],
        );

        self::assertSame(
            $proxyResponse,
            $result,
        );
    }

    public function testRoutesGroupRequestCorrectly() : void {
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $proxyResponse = $this->createMock(ResponseInterface::class);

        $proxyService = $this->createMock(ProxyService::class);

        $proxyService
            ->expects(self::once())
            ->method('forward')
            ->with(
                $request,
                'groups',
                'group-levels',
            )
            ->willReturn($proxyResponse);

        $action = new ProxyAction(
            $this->createMock(LoggerInterface::class),
            $proxyService,
        );

        $result = $action(
            $request,
            $response,
            [
                'service' => 'groups',
                'path' => 'group-levels',
            ],
        );

        self::assertSame(
            $proxyResponse,
            $result,
        );
    }

    public function testPassesEmptyPathWhenMissing() : void {
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $proxyResponse = $this->createMock(ResponseInterface::class);

        $proxyService = $this->createMock(ProxyService::class);

        $proxyService
            ->expects(self::once())
            ->method('forward')
            ->with(
                $request,
                'auth',
                '',
            )
            ->willReturn($proxyResponse);

        $action = new ProxyAction(
            $this->createMock(LoggerInterface::class),
            $proxyService,
        );

        $result = $action(
            $request,
            $response,
            [
                'service' => 'auth',
            ],
        );

        self::assertSame(
            $proxyResponse,
            $result,
        );
    }
}
