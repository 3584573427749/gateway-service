<?php

declare(strict_types=1);
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();
$env = $_ENV['APP_ENV'] ?? 'development';

return [
    'paths' => [
        'migrations' => 'migrations',
        'seeds' => 'seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $env,
        $env => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'pass' => $_ENV['DB_PASS'],
            'port' => (int)$_ENV['DB_PORT'] ?: 3307,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
];
