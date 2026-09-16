<?php


declare(strict_types=1);

namespace Tests\Unit\Config;

use App\Config\ServiceRegistry;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ServiceRegistryTest extends TestCase {
    private function createRegistry() : ServiceRegistry {
        return new ServiceRegistry([
            'auth' => 'http://auth:8080',
            'group' => 'http://group:8080',
        ]);
    }

    public function testReturnsAuthServiceUrl() : void {
        $registry = $this->createRegistry();

        self::assertSame(
            'http://auth:8080',
            $registry->get('auth'),
        );
    }

    public function testReturnsGroupServiceUrl() : void {
        $registry = $this->createRegistry();

        self::assertSame(
            'http://group:8080',
            $registry->get('group'),
        );
    }

    public function testCanCheckIfServiceExists() : void {
        $registry = $this->createRegistry();

        self::assertTrue($registry->has('auth'));
        self::assertTrue($registry->has('group'));
    }

    public function testReturnsFalseForUnknownService() : void {
        $registry = $this->createRegistry();

        self::assertFalse($registry->has('unknown'));
    }

    public function testThrowsExceptionForUnknownService() : void {
        $registry = $this->createRegistry();

        $this->expectException(RuntimeException::class);

        $registry->get('unknown');
    }

    public function testReturnsAllRegisteredServices() : void {
        $registry = $this->createRegistry();

        self::assertSame(
            [
                'auth' => 'http://auth:8080',
                'group' => 'http://group:8080',
            ],
            $registry->all(),
        );
    }

    public function testAllContainsExpectedNumberOfServices() : void {
        $registry = $this->createRegistry();

        self::assertCount(2, $registry->all());
    }
}
