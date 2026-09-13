<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

final class CorsMiddleware implements MiddlewareInterface {
    public function process(Request $request, Handler $handler) : Response {
        $origin = $request->getHeaderLine('Origin');
        $pattern = $_ENV['CORS_ALLOW_ORIGIN_PATTERN'];

        // Preflight
        if ($request->getMethod() === 'OPTIONS') {
            $response = new \Slim\Psr7\Response(204);
        } else {
            $response = $handler->handle($request);
        }

        if ($origin && $pattern && preg_match('#' . $pattern . '#', $origin)) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Vary', 'Origin')
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response
            ->withHeader('Access-Control-Allow-Methods', $_ENV['CORS_ALLOW_METHODS'] ?? 'GET,POST,PUT,PATCH,DELETE,OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', $_ENV['CORS_ALLOW_HEADERS'] ?? 'Authorization,Content-Type,Accept');
    }
}
