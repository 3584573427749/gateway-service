<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions;

use App\Domain\Exception\DomainRecordNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

class ActionTest extends TestCase {
    /**
     * @param array<string, mixed> $body
     */
    private function createRequest(array $body = []) : ServerRequestInterface {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/');

        return $request->withParsedBody($body);
    }

    private function createResponse() : Response {
        return new Response();
    }

    private function createLogger() : LoggerInterface {
        return $this->createMock(LoggerInterface::class);
    }

    public function testInvokeReturnsResponse() : void {
        $action = new ActionDummy($this->createLogger());

        $request = $this->createRequest();
        $response = $this->createResponse();

        $result = $action($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testDomainExceptionIsConverted() : void {
        $this->expectException(HttpNotFoundException::class);

        $action = new class($this->createLogger()) extends ActionDummy {
            protected function action() : \Psr\Http\Message\ResponseInterface {
                throw new DomainRecordNotFoundException('Not found');
            }
        };

        $action(
            $this->createRequest(),
            $this->createResponse(),
            [],
        );
    }

    public function testGetFormData() : void {
        $action = new ActionDummy($this->createLogger());

        $request = $this->createRequest(['foo' => 'bar']);
        $response = $this->createResponse();

        $action($request, $response, []);

        $this->assertSame(['foo' => 'bar'], $action->publicGetFormData());
    }

    public function testResolveArgSuccess() : void {
        $action = new ActionDummy($this->createLogger());

        $action(
            $this->createRequest(),
            $this->createResponse(),
            ['id' => '123'],
        );

        $this->assertSame('123', $action->publicResolveArg('id'));
    }

    public function testResolveArgThrows() : void {
        $this->expectException(HttpBadRequestException::class);

        $action = new ActionDummy($this->createLogger());

        $action(
            $this->createRequest(),
            $this->createResponse(),
            [],
        );

        $action->publicResolveArg('missing');
    }

    public function testRespondWithData() : void {
        $action = new ActionDummy($this->createLogger());

        $request = $this->createRequest();
        $response = $this->createResponse();

        $action($request, $response, []);

        $result = $action->publicRespondWithData(['test' => 123], 201);

        $this->assertSame(201, $result->getStatusCode());

        $body = (string)$result->getBody();

        $this->assertStringContainsString('"test": 123', $body);
        $this->assertStringContainsString('"statusCode": 201', $body);
    }
}
