<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ErrorHandler;

use App\Application\ErrorHandler\ErrorHandler;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\InternalException;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\RecordExistsException;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\Exception\ValidationException;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

class ErrorHandlerTest extends TestCase {
    private function createRequest() : ServerRequestInterface {
        return (new ServerRequestFactory())
            ->createServerRequest('GET', '/test');
    }

    private function createLogger() : Logger {
        return $this->createMock(Logger::class);
    }

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    private function handle(\Throwable $exception) : array {
        $logger = $this->createLogger();

        $handler = new ErrorHandler($logger);

        $response = $handler(
            $this->createRequest(),
            $exception,
            false,
        );

        $body = (string) $response->getBody();

        return [
            'status' => $response->getStatusCode(),
            'json' => json_decode($body, true, 512, JSON_THROW_ON_ERROR),
        ];
    }

    public function testValidationException() : void {
        $result = $this->handle(new ValidationException('Invalid'));

        $this->assertSame(422, $result['status']);
        $this->assertSame('ValidationException', $result['json']['error']['type']);
    }

    public function testUnauthorizedException() : void {
        $result = $this->handle(new UnauthorizedException('No auth'));

        $this->assertSame(401, $result['status']);
    }

    public function testForbiddenException() : void {
        $result = $this->handle(new ForbiddenException('Forbidden'));

        $this->assertSame(403, $result['status']);
    }

    public function testNotFoundException() : void {
        $result = $this->handle(new NotFoundException('Missing'));

        $this->assertSame(404, $result['status']);
    }

    public function testRecordExistsException() : void {
        $result = $this->handle(new RecordExistsException('Exists'));

        $this->assertSame(409, $result['status']);
    }

    public function testInternalException() : void {
        $result = $this->handle(new InternalException('Error'));

        $this->assertSame(500, $result['status']);
    }

    public function testUnknownExceptionDefaultsTo500() : void {
        $result = $this->handle(new \RuntimeException('Unknown'));

        $this->assertSame(500, $result['status']);
        $this->assertSame('RuntimeException', $result['json']['error']['type']);
    }

    public function testPayloadContainsMessage() : void {
        $result = $this->handle(new ValidationException('Bad input'));

        $this->assertSame('Bad input', $result['json']['error']['message']);
    }

    public function testPayloadContainsDetails() : void {
        $exception = new ValidationException('Invalid', ['field' => 'email']);

        $result = $this->handle($exception);

        $this->assertSame(['field' => 'email'], $result['json']['error']['details']);
    }

    public function testLogsError() : void {
        $logger = $this->createMock(Logger::class);

        $logger->expects($this->once())
            ->method('error');

        $handler = new ErrorHandler($logger);

        $handler(
            $this->createRequest(),
            new ValidationException('Error'),
            false,
        );
    }
}
