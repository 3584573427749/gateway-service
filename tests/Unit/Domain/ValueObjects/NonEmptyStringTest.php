<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObjects\NonEmptyString;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NonEmptyStringTest extends TestCase {
    public function testValidStringIsAccepted() : void {
        $value = new NonEmptyString('hello');

        $this->assertSame('hello', $value->toString());
    }

    public function testStringIsTrimmed() : void {
        $value = new NonEmptyString('  hello world  ');

        $this->assertSame('hello world', (string)$value);
    }

    public function testEmptyStringThrowsException() : void {
        $this->expectException(InvalidArgumentException::class);

        new NonEmptyString('');
    }

    public function testWhitespaceOnlyThrowsException() : void {
        $this->expectException(InvalidArgumentException::class);

        new NonEmptyString('     ');
    }

    public function testEqualsWithSameValue() : void {
        $a = new NonEmptyString('test');
        $b = new NonEmptyString('test');

        $this->assertTrue($a->equals($b));
    }

    public function testEqualsWithDifferentValue() : void {
        $a = new NonEmptyString('test');
        $b = new NonEmptyString('other');

        $this->assertFalse($a->equals($b));
    }
}
