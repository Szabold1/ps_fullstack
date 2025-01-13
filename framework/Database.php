<?php

namespace Framework;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Database
{
    public PDO $connection;

    public function __construct(array $config)
    {
        // Data Source Name
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        // Options for the PDO connection
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];

        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);

            // bind the parameters to the query
            foreach ($params as $param => $value) {
                $stmt->bindValue(":{$param}", $value);
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: {$e->getMessage()}");
        }
    }
}
