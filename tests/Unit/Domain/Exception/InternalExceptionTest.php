<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\InternalException;
use PHPUnit\Framework\TestCase;

class InternalExceptionTest extends TestCase {
    public function testCanBeInstantiated() : void {
        $exception = new InternalException('Internal error');

        $this->assertInstanceOf(InternalException::class, $exception);
    }

    public function testMessageIsStored() : void {
        $exception = new InternalException('Something went wrong');

        $this->assertSame('Something went wrong', $exception->getMessage());
    }

    public function testExtendsRuntimeException() : void {
        $exception = new InternalException('Test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testDetailsAreStored() : void {
        $details = [
            'service' => 'database',
            'operation' => 'insert',
        ];

        $exception = new InternalException('Failure', $details);

        $this->assertSame($details, $exception->getDetails());
    }

    public function testDetailsDefaultToEmptyArray() : void {
        $exception = new InternalException('Failure');

        $this->assertSame([], $exception->getDetails());
    }

    public function testCanBeThrownAndCaught() : void {
        $this->expectException(InternalException::class);
        $this->expectExceptionMessage('Boom');

        throw new InternalException('Boom');
    }
}
