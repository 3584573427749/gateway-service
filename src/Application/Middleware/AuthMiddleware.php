<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

final class AuthMiddleware implements MiddlewareInterface {
    public function process(Request $request, Handler $handler) : Response {
        $verified = $request->getHeaderLine('X-Auth-Verified');
        $userId = $request->getHeaderLine('X-User-Id');
        $roles = $request->getHeaderLine('X-User-Roles');

        if ($verified !== 'true' || empty($userId)) {
            throw new UnauthorizedException('Authentication required');
        }

        // Lägg auth‑kontext i request‑attributes
        $request = $request
            ->withAttribute('userId', $userId)
            ->withAttribute('roles', $roles ? explode(',', $roles) : []);

        return $handler->handle($request);
    }
}
