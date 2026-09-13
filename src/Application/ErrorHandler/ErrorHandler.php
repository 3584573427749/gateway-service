<?php

declare(strict_types=1);

namespace App\Application\ErrorHandler;

use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\InternalException;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\RecordExistsException;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\Exception\ValidationException;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

class ErrorHandler {
    public function __construct(private Logger $logger) {
    }

    public function __invoke(Request $request, Throwable $exception, bool $displayErrorDetails) : Response {
        $status = $this->mapStatus($exception);
        $payload = $this->buildPayload($exception, $status);

        // Logga ALLT
        $this->logger->error(sprintf('%s (%d): %s', $exception::class, $status, $exception->getMessage()), ['path' => $request->getUri()->getPath(), 'method' => $request->getMethod(), 'details' => method_exists($exception, 'getDetails') ? $exception->getDetails() : null]);

        $response = new \Slim\Psr7\Response($status);
        $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(Throwable $exception, int $status) : array {
        return ['statusCode' => $status,
            'error' => [
                'type' => (new \ReflectionClass($exception))->getShortName(),
                'message' => $exception->getMessage(),
                'details' => method_exists($exception, 'getDetails') ? $exception->getDetails() : null,
            ],
        ];
    }

    private function mapStatus(Throwable $exception) : int {
        return match (true) {
            $exception instanceof \InvalidArgumentException => 400,
            $exception instanceof UnauthorizedException => 401,
            $exception instanceof ForbiddenException => 403,
            $exception instanceof NotFoundException => 404,
            $exception instanceof RecordExistsException => 409,
            $exception instanceof ValidationException => 422,
            $exception instanceof InternalException => 500,
            default => 500,
        };
    }
}
