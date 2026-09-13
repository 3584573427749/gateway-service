<?php

declare(strict_types=1);

use App\Domain\ValueObjects\AbstractId;
use PHPUnit\Framework\TestCase;

final class DummyId extends AbstractId {
}

final class AbstractIdTest extends TestCase {
    public function testNewIdGeneratesUuidV7() : void {
        $id = new DummyId();

        $this->assertNotEmpty($id->toString());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f-]{36}$/',
            (string)$id,
        );
    }

    public function testFromStringAcceptsValidUuid() : void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $id = DummyId::fromString($uuid);

        $this->assertSame($uuid, $id->toString());
    }

    public function testInvalidUuidThrowsException() : void {
        $this->expectException(InvalidArgumentException::class);

        new DummyId('not-a-uuid');
    }

    public function testEqualsWithSameTypeAndValue() : void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $a = new DummyId($uuid);
        $b = new DummyId($uuid);

        $this->assertTrue($a->equals($b));
    }

    public function testEqualsWithDifferentTypeThrows() : void {
        $this->expectException(InvalidArgumentException::class);

        $a = new DummyId();
        $b = new class() extends AbstractId {};

        $a->equals($b);
    }
}
