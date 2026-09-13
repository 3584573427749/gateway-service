<?php

declare(strict_types=1);

namespace TestsException;

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\TestCase;

class UnauthorizedExceptionTest extends TestCase {
    public function testCanBeInstantiated() : void {
        $exception = new UnauthorizedException('Unauthorized');

        $this->assertInstanceOf(UnauthorizedException::class, $exception);
    }

    public function testMessageIsStored() : void {
        $exception = new UnauthorizedException('Access denied');

        $this->assertSame('Access denied', $exception->getMessage());
    }

    public function testExtendsRuntimeException() : void {
        $exception = new UnauthorizedException('Test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testDetailsAreStored() : void {
        $details = [
            'token' => 'invalid',
            'reason' => 'expired',
        ];

        $exception = new UnauthorizedException('Unauthorized', $details);

        $this->assertSame($details, $exception->getDetails());
    }

    public function testDetailsDefaultToEmptyArray() : void {
        $exception = new UnauthorizedException('Unauthorized');

        $this->assertSame([], $exception->getDetails());
    }

    public function testCanBeThrownAndCaught() : void {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized');

        throw new UnauthorizedException('Unauthorized');
    }
}
