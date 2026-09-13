<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions;

use App\Http\Actions\ActionError;
use App\Http\Actions\ActionPayload;
use PHPUnit\Framework\TestCase;

class ActionPayloadTest extends TestCase {
    public function testConstructorAndGetters() : void {
        $error = new ActionError(ActionError::SERVER_ERROR, 'Failure');

        $payload = new ActionPayload(500, ['foo' => 'bar'], $error);

        $this->assertSame(500, $payload->getStatusCode());
        $this->assertSame(['foo' => 'bar'], $payload->getData());
        $this->assertSame($error, $payload->getError());
    }

    public function testJsonSerializeWithData() : void {
        $payload = new ActionPayload(200, ['key' => 'value']);

        $data = $payload->jsonSerialize();

        $this->assertSame([
            'statusCode' => 200,
            'data' => ['key' => 'value'],
        ], $data);
    }

    public function testJsonSerializeWithError() : void {
        $error = new ActionError(ActionError::NOT_FOUND, 'Not found');

        $payload = new ActionPayload(404, null, $error);

        $data = $payload->jsonSerialize();

        $this->assertSame([
            'statusCode' => 404,
            'error' => $error,
        ], $data);
    }

    public function testJsonSerializePrefersDataOverError() : void {
        $error = new ActionError(ActionError::SERVER_ERROR, 'Failure');

        $payload = new ActionPayload(200, ['ok' => true], $error);

        $data = $payload->jsonSerialize();

        // Viktigt: data vinner över error enligt implementationen
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayNotHasKey('error', $data);
    }

    public function testJsonSerializeWithNeitherDataNorError() : void {
        $payload = new ActionPayload(204);

        $data = $payload->jsonSerialize();

        $this->assertSame([
            'statusCode' => 204,
        ], $data);
    }

    public function testJsonEncodeWithData() : void {
        $payload = new ActionPayload(200, ['test' => 123]);

        $json = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('statusCode', $json);
        $this->assertStringContainsString('test', $json);
    }

    public function testJsonEncodeWithError() : void {
        $error = new ActionError(ActionError::VALIDATION_ERROR, 'Invalid');

        $payload = new ActionPayload(400, null, $error);

        $json = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('VALIDATION_ERROR', $json);
        $this->assertStringContainsString('Invalid', $json);
    }
}
