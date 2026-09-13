<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

abstract class DatabaseBaseTestCase extends TestCase {
    protected Connection $connection;

    protected function setUp() : void {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1:3307',
            'user' => 'root',
            'password' => '',
        ]);


        $this->connection->executeStatement('CREATE DATABASE IF NOT EXISTS test_db');
        $this->connection->executeStatement('USE test_db');

    }

    protected function tearDown() : void {
        $this->connection->executeStatement('DROP DATABASE test_db');
    }

    protected function loadSchema(string $table) : void {
        $schema = file_get_contents(__DIR__ . "/../../../../var/schema/{$table}.sql");
        if ($schema === false) {
            return;
        }
        $this->connection->executeStatement("DROP TABLE IF EXISTS {$table}");
        $this->connection->executeStatement($schema);
    }
}
