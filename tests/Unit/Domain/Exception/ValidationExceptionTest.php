<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase {
    public function testCanBeInstantiated() : void {
        $exception = new ValidationException('Validation failed');

        $this->assertInstanceOf(ValidationException::class, $exception);
    }

    public function testMessageIsStored() : void {
        $exception = new ValidationException('Invalid input');

        $this->assertSame('Invalid input', $exception->getMessage());
    }

    public function testExtendsRuntimeException() : void {
        $exception = new ValidationException('Test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testDetailsAreStored() : void {
        $details = [
            'field' => 'email',
            'error' => 'invalid format',
        ];

        $exception = new ValidationException('Validation error', $details);

        $this->assertSame($details, $exception->getDetails());
    }

    public function testDetailsDefaultToEmptyArray() : void {
        $exception = new ValidationException('Validation error');

        $this->assertSame([], $exception->getDetails());
    }

    public function testCanBeThrownAndCaught() : void {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation error');

        throw new ValidationException('Validation error');
    }
}
