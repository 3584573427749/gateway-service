<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

final class RequireRoleMiddleware implements MiddlewareInterface {
    /** @var string[] */
    private array $requiredRoles;

    /**
     * @param string[] $roles Minst en av dessa roller krävs
     */
    public function __construct(array $roles) {
        // normalisera till lowercase för case‑insensitiv jämförelse
        $this->requiredRoles = array_map('strtolower', $roles);
    }

    public function process(Request $request, Handler $handler) : Response {
        $roles = $request->getAttribute('roles', []);

        // Normalisera roller från auth‑middleware
        $userRoles = array_map('strtolower', (array)$roles);

        foreach ($this->requiredRoles as $required) {
            if (in_array($required, $userRoles, true)) {
                return $handler->handle($request);
            }
        }

        throw new ForbiddenException('Insufficient permissions');
    }
}
