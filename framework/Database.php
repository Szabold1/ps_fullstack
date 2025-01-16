<?php

declare(strict_types=1);

namespace Framework;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $connection;

    public function __construct(array $config)
    {
        // data Source Name
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        // options for the PDO connection
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    private function query(string $sql, array $params = []): PDOStatement
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

    public function getUserByType(string $type, string $value): array|null
    {
        $validTypes = ['email', 'id'];
        if (!in_array($type, $validTypes)) {
            throw new Exception("Invalid type: {$type}");
        }

        $result = $this->query("SELECT * FROM users WHERE {$type} = :{$type}", [$type => $value])->fetch();
        return $result === false ? null : $result;
    }

    public function createUser(array $data): array|null
    {
        $this->query("INSERT INTO users (email, nickname, birthdate, password_hash) VALUES (:email, :nickname, :birthdate, :password_hash)", $data);

        // add the id of the user to data
        $data['id'] = $this->connection->lastInsertId();

        return $data['id'] ? $data : null;
    }

    public function updateUser(array $data): array|null
    {
        // filter out null values
        $validData = array_filter($data, function ($value) {
            return $value !== null;
        });

        $fieldsToUpdate = [];
        foreach ($validData as $key => $value) {
            $fieldsToUpdate[] = "{$key} = :{$key}";
        }
        $fieldsToUpdate = implode(', ', $fieldsToUpdate);

        $this->query("UPDATE users SET {$fieldsToUpdate} WHERE id = :id", $validData);

        return $this->getUserByType('id', $validData['id']);
    }
}
