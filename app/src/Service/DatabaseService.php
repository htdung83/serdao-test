<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;

class DatabaseService
{
    /**
     * @throws Exception
     */
    public function __construct(
        private Connection $connection
    ) {
        $connectionParams = [
            'dbname' => 'symfony',
            'user' => 'symfony',
            'password' => '',
            'host' => 'mariadb',
            'driver' => 'pdo_mysql',
        ];

        $this->connection = DriverManager::getConnection($connectionParams);
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @throws Exception
     */
    public function statement($sql): Statement
    {
        return $this->connection->prepare($sql);
    }

    /**
     * @throws Exception
     */
    public function execute(string $sql): array
    {
        return $this->statement($sql)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}