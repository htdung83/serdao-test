<?php

namespace App\Repository;

use App\Service\DatabaseService;
use Doctrine\DBAL\Exception;

class UserRepository
{
    /**
     * @throws Exception
     */
    public function __construct(
        private DatabaseService $connection
    ) {
        $this->createTableIfNotExist();
    }

    /**
     * @throws Exception
     */
    public function search()
    {
        return $this->connection->execute(
            "SELECT * FROM user;"
        );
    }

    /**
     * @throws Exception
     */
    public function save($firstName, $lastName, $address)
    {
        $this->connection->execute(
            "INSERT INTO user(id, data) values(" . time() . ", '{$firstName} - {$lastName} - {$address}');"
        );
    }

    /**
     * @throws Exception
     */
    public function delete($id)
    {
        $this->connection->execute(
            "DELETE FROM user WHERE id = {$id};"
        );
    }

    /**
     * @throws Exception
     */
    public function createTableIfNotExist(): void
    {
        $tableExists = $this->connection->execute(
            "SELECT * FROM information_schema.tables WHERE table_schema = 'symfony' AND table_name = 'user' LIMIT 1;"
        );

        if (empty($tableExists)) {
            $this->connection->execute("CREATE TABLE user (id int, data varchar(255))");

            $this->fixtures();
        }
    }

    /**
     * @throws Exception
     */
    public function fixtures(): void
    {
        $users = [
            ['id' => 1, 'data' => 'Barack - Obama - White House'],
            ['id' => 2, 'data' => 'Britney - Spears - America'],
            ['id' => 3, 'data' => 'Leonardo - DiCaprio - Titanic'],
        ];

        foreach ($users as $user) {
            $this->connection->execute(
                "INSERT INTO user(id, data) values({$user['id']}, '{$user['data']}');"
            );
        }
    }
}