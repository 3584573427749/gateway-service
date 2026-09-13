<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Infrastructure\Auth\AuthServiceClient;
use App\Application\Settings;
use App\Domain\Exception\UnauthorizedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

final class AuthServiceMiddleware implements MiddlewareInterface {
    public function __construct(
        private AuthServiceClient $client,
        private Settings $settings
    ) {}

    public function process(Request $request, Handler $handler):Response {
        $token = $request->getHeaderLine('X-Service-Token');
        $serviceName = $this->settings->get('SERVICE_NAME');

        if (!$token) {
            throw new UnauthorizedException('Missing service token');
        }

        $result = $this->client->validateServiceToken($token, $serviceName);

        if (($result['valid'] ?? false) !== true) {
            throw new UnauthorizedException('Invalid service token');
        }

        return $handler->handle($request);
    }
}