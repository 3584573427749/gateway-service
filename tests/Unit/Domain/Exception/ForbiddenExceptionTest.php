<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\ForbiddenException;
use PHPUnit\Framework\TestCase;

class ForbiddenExceptionTest extends TestCase {
    public function testCanBeInstantiated() : void {
        $exception = new ForbiddenException('Forbidden');

        $this->assertInstanceOf(ForbiddenException::class, $exception);
    }

    public function testMessageIsStored() : void {
        $exception = new ForbiddenException('Access denied');

        $this->assertSame('Access denied', $exception->getMessage());
    }

    public function testExtendsRuntimeException() : void {
        $exception = new ForbiddenException('Test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testDetailsAreStored() : void {
        $details = [
            'role' => 'user',
            'action' => 'delete',
        ];

        $exception = new ForbiddenException('Forbidden', $details);

        $this->assertSame($details, $exception->getDetails());
    }

    public function testDetailsDefaultToEmptyArray() : void {
        $exception = new ForbiddenException('Forbidden');

        $this->assertSame([], $exception->getDetails());
    }

    public function testCanBeThrownAndCaught() : void {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Forbidden');

        throw new ForbiddenException('Forbidden');
    }
}
