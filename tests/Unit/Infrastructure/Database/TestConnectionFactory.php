<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

final class TestConnectionFactory {
    public static function create() : Connection {
        return DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1:3307',
            'user' => 'root',
            'password' => '',
            'dbname' => 'test_db',
        ]);
    }
}
