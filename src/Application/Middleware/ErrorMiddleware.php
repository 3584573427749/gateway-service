<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\ErrorHandler\ErrorHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Throwable;

class ErrorMiddleware implements MiddlewareInterface {
    public function __construct(private ErrorHandler $handler) {
    }

    public function process(Request $request, Handler $handler) : Response {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return ($this->handler)($request, $e, false);
        }
    }
}
