<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ErrorHandler;

use App\Application\ErrorHandler\ErrorHandler;
use App\Application\Middleware\ErrorMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class ErrorMiddlewareTest extends TestCase {
    private function createRequest() : ServerRequestInterface {
        return (new ServerRequestFactory())
            ->createServerRequest('GET', '/test');
    }

    public function testPassesThroughWhenNoException() : void {
        $request = $this->createRequest();
        $response = new Response();

        // Mock handler (nästa middleware / controller)
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        // Mock ErrorHandler (ska INTE användas här)
        $errorHandler = $this->createMock(ErrorHandler::class);
        $errorHandler->expects($this->never())->method('__invoke');

        $middleware = new ErrorMiddleware($errorHandler);

        $result = $middleware->process($request, $handler);

        $this->assertSame($response, $result);
    }

    public function testCallsErrorHandlerOnException() : void {
        $request = $this->createRequest();
        $exception = new \RuntimeException('Boom');
        $expectedResponse = new Response(500);

        // Handler kastar exception
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willThrowException($exception);

        // ErrorHandler ska anropas
        $errorHandler = $this->createMock(ErrorHandler::class);

        $errorHandler->expects($this->once())
            ->method('__invoke')
            ->with(
                $request,
                $exception,
                false,
            )
            ->willReturn($expectedResponse);

        $middleware = new ErrorMiddleware($errorHandler);

        $result = $middleware->process($request, $handler);

        $this->assertSame($expectedResponse, $result);
    }
}
