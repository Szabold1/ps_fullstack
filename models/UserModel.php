<?php

namespace Models;

use Framework\Database;
use Framework\Response;

class UserModel
{
    private $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    // Get user data from database or file, based on the email of the user
    public function getUserByEmail(string $email)
    {
        if (rand(0, 1) === 0) {
            return $this->db->query("SELECT * FROM users WHERE email = :email", ['email' => $email])->fetch();
        } else {
            $filePath = basePath('data/users.json');

            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                $users = json_decode($fileContent, true) ?? [];
                foreach ($users as $user) {
                    if (isset($user['email']) && $user['email'] === $email) {
                        return (object)$user;
                    }
                }
            }

            return null;
        }
    }

    private function storeUserInFile(array $data)
    {
        $filePath = basePath('data/users.json');

        $users = [];
        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);
            $users = json_decode($fileContent, true) ?? [];
        }

        $users[] = $data;
        file_put_contents($filePath, json_encode($users));
    }

    public function create(array $data)
    {
        // check if user email already exists
        $user = $this->getUserByEmail($data['email']);
        if ($user) {
            http_response_code(Response::$CONFLICT);
            loadView('register', [
                'errors' => ['email' => 'Az email cím már foglalt'],
                'user' => [
                    'email' => $data['email'],
                    'nickname' => $data['nickname'],
                    'birthdate' => $data['birthdate']
                ]
            ]);
            exit;
        }

        // insert user data into database
        $this->db->query("INSERT INTO users (email, nickname, birthdate, password_hash) VALUES (:email, :nickname, :birthdate, :password_hash)", $data);

        // add the id of the user to data
        $data['id'] = $this->db->connection->lastInsertId();

        // store user data in a file
        $this->storeUserInFile($data);
    }

    public function login(array $data)
    {
        // check for email and password
        $user = $this->getUserByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user->password_hash)) {
            http_response_code(Response::$UNAUTHORIZED);
            loadView('login', [
                'errors' => ['general' => 'Helytelen email cím vagy jelszó'],
                'user' => [
                    'email' => $data['email']
                ]
            ]);
            exit;
        }

        return $user;
    }
}
