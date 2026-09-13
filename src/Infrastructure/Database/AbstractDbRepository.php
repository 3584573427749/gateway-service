<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Doctrine\DBAL\Connection;

abstract class AbstractDbRepository {
    protected Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    /**
     * Starta transaktion
     */
    protected function beginTransaction() : void {
        $this->connection->beginTransaction();
    }

    /**
     * Commit transaktion
     */
    protected function commit() : void {
        $this->connection->commit();
    }

    /**
     * Rollback transaktion
     */
    protected function rollback() : void {
        $this->connection->rollBack();
    }
}
