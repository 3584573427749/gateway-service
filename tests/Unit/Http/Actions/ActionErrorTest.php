<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions;

use App\Http\Actions\ActionError;
use PHPUnit\Framework\TestCase;

class ActionErrorTest extends TestCase {
    public function testConstructorSetsValues() : void {
        $error = new ActionError(ActionError::NOT_FOUND, 'Missing resource');

        $this->assertSame(ActionError::NOT_FOUND, $error->getType());
        $this->assertSame('Missing resource', $error->getDescription());
    }

    public function testDescriptionDefaultsToNull() : void {
        $error = new ActionError(ActionError::SERVER_ERROR);

        $this->assertNull($error->getDescription());
    }

    public function testSettersWorkAndAreChainable() : void {
        $error = new ActionError(ActionError::BAD_REQUEST);

        $result = $error
            ->setType(ActionError::VALIDATION_ERROR)
            ->setDescription('Invalid input');

        $this->assertSame($error, $result);
        $this->assertSame(ActionError::VALIDATION_ERROR, $error->getType());
        $this->assertSame('Invalid input', $error->getDescription());
    }

    public function testCanResetDescriptionToNull() : void {
        $error = new ActionError(ActionError::BAD_REQUEST, 'Error');

        $error->setDescription(null);

        $this->assertNull($error->getDescription());
    }

    public function testJsonSerializeWithDescription() : void {
        $error = new ActionError(ActionError::NOT_FOUND, 'Not found');

        $data = $error->jsonSerialize();

        $this->assertSame([
            'type' => ActionError::NOT_FOUND,
            'description' => 'Not found',
        ], $data);
    }

    public function testJsonSerializeWithoutDescription() : void {
        $error = new ActionError(ActionError::SERVER_ERROR);

        $data = $error->jsonSerialize();

        $this->assertSame([
            'type' => ActionError::SERVER_ERROR,
            'description' => null,
        ], $data);
    }

    public function testJsonEncodeWorks() : void {
        $error = new ActionError(ActionError::VALIDATION_ERROR, 'Invalid');

        $json = json_encode($error, JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('VALIDATION_ERROR', $json);
        $this->assertStringContainsString('Invalid', $json);
    }
}
