<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class NotFoundExceptionTest extends TestCase {
    public function testCanBeInstantiated() : void {
        $exception = new NotFoundException('Not found');

        $this->assertInstanceOf(NotFoundException::class, $exception);
    }

    public function testMessageIsStored() : void {
        $exception = new NotFoundException('Resource not found');

        $this->assertSame('Resource not found', $exception->getMessage());
    }

    public function testExtendsRuntimeException() : void {
        $exception = new NotFoundException('Test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testDetailsAreStored() : void {
        $details = [
            'entity' => 'user',
            'id' => '123',
        ];

        $exception = new NotFoundException('Not found', $details);

        $this->assertSame($details, $exception->getDetails());
    }

    public function testDetailsDefaultToEmptyArray() : void {
        $exception = new NotFoundException('Not found');

        $this->assertSame([], $exception->getDetails());
    }

    public function testCanBeThrownAndCaught() : void {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Boom');

        throw new NotFoundException('Boom');
    }
}
